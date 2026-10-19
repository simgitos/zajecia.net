<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\BillingItem;
use App\Models\Course;
use App\Models\School;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    private array $polishMonths = [
        1 => 'styczeń', 2 => 'luty', 3 => 'marzec', 4 => 'kwiecień',
        5 => 'maj', 6 => 'czerwiec', 7 => 'lipiec', 8 => 'sierpień',
        9 => 'wrzesień', 10 => 'październik', 11 => 'listopad', 12 => 'grudzień'
    ];

    /**
     * Zwraca gotowe szablony komunikatów do szybkiego wyboru w formularzu.
     *
     * @return array<int, array<string, string>>
     */
    public function getQuickTemplates(): array
    {
        return [
            [
                'id' => 'cancellation',
                'name' => 'Odwołanie zajęć (nagłe / losowe)',
                'title' => 'Odwołanie najbliższych zajęć',
                'message' => "Szanowni Państwo,\n\nInformujemy, że z przyczyn niezależnych najbliższe zajęcia zostają odwołane. Informację o terminie odrobienia zajęć przekażemy w osobnym komunikacie. Przepraszamy za niedogodności.",
                'type' => 'warning',
                'url' => '/dashboard',
                'action_text' => 'Przejdź do panelu',
            ],
            [
                'id' => 'reschedule',
                'name' => 'Zmiana terminu lub sali zajęć',
                'title' => 'Zmiana terminu / sali zajęć',
                'message' => "Dzień dobry,\n\nUprzejmie informujemy o zmianie harmonogramu najbliższych zajęć. Prosimy o sprawdzenie szczegółów w panelu oraz przybycie o zaktualizowanej porze.",
                'type' => 'info',
                'url' => '/dashboard',
                'action_text' => 'Zobacz plan',
            ],
            [
                'id' => 'billing_summary',
                'name' => 'Podsumowanie rozliczeń miesiąca',
                'title' => 'Podsumowanie opłat za zajęcia',
                'message' => "Szanowni Państwo,\n\nZostały przygotowane i zaktualizowane rozliczenia za zajęcia za miniony miesiąc. Prosimy o sprawdzenie zestawienia w zakładce Płatności i terminowe uregulowanie opłat.",
                'type' => 'info',
                'url' => '/user/platnosci',
                'action_text' => 'Przejdź do płatności',
            ],
            [
                'id' => 'payment_reminder',
                'name' => 'Pilne przypomnienie o zaległej płatności',
                'title' => 'Przypomnienie o zaległych płatnościach',
                'message' => "Dzień dobry,\n\nPrzypominamy o konieczności uregulowania zaległych opłat za zajęcia. Płatności można dokonać bezpośrednio i bezpiecznie online w panelu.",
                'type' => 'danger',
                'url' => '/user/platnosci',
                'action_text' => 'Opłać zaległości',
            ],
            [
                'id' => 'organizational',
                'name' => 'Komunikat organizacyjny / Ogłoszenie ogólne',
                'title' => 'Ważny komunikat organizacyjny',
                'message' => "Drodzy Uczestnicy i Rodzice,\n\nZapraszamy do zapoznania się z nowymi informacjami organizacyjnymi dotyczącymi funkcjonowania naszej placówki oraz zbliżających się wydarzeń.",
                'type' => 'info',
                'url' => '/dashboard',
                'action_text' => 'Zobacz szczegóły',
            ],
            [
                'id' => 'instructors_meeting',
                'name' => 'Wiadomość do kadry instruktorskiej',
                'title' => 'Komunikat dla instruktorów i prowadzących',
                'message' => "Drodzy Instruktorzy,\n\nPrzypominamy o bieżącym uzupełnianiu list obecności w dzienniku elektronicznym oraz o zaplanowanym zebraniu kadry. W razie pytań prosimy o kontakt z dyrekcją placówki.",
                'type' => 'info',
                'url' => '/teacher/lessons',
                'action_text' => 'Dziennik zajęć',
            ],
        ];
    }

    /**
     * Rozwiązuje listę odbiorców w zależności od wybranego typu i uprawnień nadawcy.
     *
     * @return Collection<int, User>
     */
    public function resolveRecipients(
        User $sender,
        string $targetType,
        ?int $courseId = null,
        ?int $userId = null
    ): Collection {
        $school = $sender->activeSchool();
        if (!$school) {
            return collect();
        }

        switch ($targetType) {
            case 'group':
                if (!$courseId) {
                    return collect();
                }

                $courseQuery = Course::where('id', $courseId)
                    ->where('school_id', $school->id);

                // Jeżeli wysyła nauczyciel bez uprawnień admina, musi być instruktorem tego kursu
                if ($sender->hasRole(UserRole::TEACHER) && !$sender->hasRole(UserRole::ADMIN)) {
                    $courseQuery->where('instructor_id', $sender->id);
                }

                $course = $courseQuery->first();
                if (!$course) {
                    return collect();
                }

                // Pobierz unikalnych rodziców dzieci zapisanych na ten kurs
                return $course->children()
                    ->with('parent')
                    ->get()
                    ->pluck('parent')
                    ->filter()
                    ->unique('id')
                    ->values();

            case 'instructors':
                // Tylko instruktorzy powiązani z bieżącą szkołą
                return $school->users()
                    ->get()
                    ->filter(fn (User $user) => $user->rolesForSchool($school)->contains(UserRole::TEACHER))
                    ->values();

            case 'single_user':
                if (!$userId) {
                    return collect();
                }

                $user = $school->users()->where('users.id', $userId)->first();
                return $user ? collect([$user]) : collect();

            case 'all_parents':
                // Wszyscy użytkownicy szkoły mający rolę rodzica/użytkownika
                return $school->users()
                    ->get()
                    ->filter(fn (User $user) => $user->rolesForSchool($school)->contains(UserRole::USER))
                    ->values();

            default:
                return collect();
        }
    }

    /**
     * Wysyła powiadomienie do wyselekcjonowanych odbiorców.
     *
     * @param array<string, mixed> $data
     */
    public function sendNotification(User $sender, array $data): int
    {
        $school = $sender->activeSchool();
        $targetType = (string) ($data['target_type'] ?? '');
        $courseId = !empty($data['course_id']) ? (int) $data['course_id'] : null;
        $userId = !empty($data['user_id']) ? (int) $data['user_id'] : null;

        $recipients = $this->resolveRecipients($sender, $targetType, $courseId, $userId);

        if ($recipients->isEmpty()) {
            return 0;
        }

        $notification = new GeneralNotification(
            title: (string) $data['title'],
            message: (string) $data['message'],
            type: (string) ($data['type'] ?? 'info'),
            url: !empty($data['url']) ? (string) $data['url'] : null,
            senderName: $sender->name . ($school ? " ({$school->name})" : ''),
            schoolId: $school?->id,
            actionText: !empty($data['action_text']) ? (string) $data['action_text'] : null
        );

        Notification::send($recipients, $notification);

        return $recipients->count();
    }

    /**
     * Generuje i rozsyła powiadomienia z podsumowaniem rozliczeń wybranego miesiąca do rodziców.
     */
    public function sendMonthlyBillingSummary(
        User $admin,
        string $yearMonth,
        string $targetGroup = 'all', // 'all' (wszyscy z pozycjami) lub 'unpaid' (tylko z zaległościami)
        ?string $customNote = null
    ): int {
        $school = $admin->activeSchool();
        if (!$school) {
            return 0;
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $monthName = ($this->polishMonths[$date->month] ?? $date->format('F')) . ' ' . $date->year;
        } catch (\Exception) {
            $monthName = $yearMonth;
        }

        $itemsQuery = BillingItem::where('school_id', $school->id)
            ->where('year_month', $yearMonth)
            ->where('amount', '>', 0)
            ->with(['parent', 'child', 'course']);

        if ($targetGroup === 'unpaid') {
            $itemsQuery->where('status', 'unpaid');
        }

        $items = $itemsQuery->get();
        if ($items->isEmpty()) {
            return 0;
        }

        // Grupowanie pozycji per rodzic
        $grouped = $items->groupBy('parent_id');
        $notifiedCount = 0;

        foreach ($grouped as $parentId => $parentItems) {
            /** @var User|null $parent */
            $parent = $parentItems->first()?->parent;
            if (!$parent) {
                continue;
            }

            $totalParentAmount = (float) $parentItems->sum('amount');
            $unpaidParentAmount = (float) $parentItems->where('status', 'unpaid')->sum('amount');

            if ($targetGroup === 'unpaid' && $unpaidParentAmount <= 0) {
                continue;
            }

            if ($unpaidParentAmount > 0) {
                $title = "Podsumowanie opłat za {$monthName} – do zapłaty: " . number_format($unpaidParentAmount, 2, ',', ' ') . " zł";
                $message = "Dzień dobry!\n\nZostało wygenerowane podsumowanie opłat za zajęcia za okres: {$monthName}.\n";
                $message .= "Łączna kwota pozostała do uregulowania: " . number_format($unpaidParentAmount, 2, ',', ' ') . " zł.\n";
                if ($customNote) {
                    $message .= "\nUwaga od dyrekcji: {$customNote}\n";
                }
                $message .= "\nProsimy o wygodną płatność online w panelu rodzica.";
                $type = 'warning';
            } else {
                $title = "Podsumowanie rozliczeń za {$monthName} – opłacone";
                $message = "Dzień dobry!\n\nInformujemy, że wszystkie opłaty za zajęcia za okres {$monthName} (suma: " . number_format($totalParentAmount, 2, ',', ' ') . " zł) zostały zaksięgowane jako opłacone. Dziękujemy!";
                if ($customNote) {
                    $message .= "\n\nInformacja dodatkowa: {$customNote}";
                }
                $type = 'success';
            }

            $notification = new GeneralNotification(
                title: $title,
                message: $message,
                type: $type,
                url: route('user.payments.index', [], false),
                senderName: $admin->name . " ({$school->name})",
                schoolId: $school->id,
                actionText: 'Zobacz płatności'
            );

            $parent->notify($notification);
            $notifiedCount++;
        }

        return $notifiedCount;
    }
}
