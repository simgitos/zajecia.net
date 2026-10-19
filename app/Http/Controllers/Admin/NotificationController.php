<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Models\Course;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Formularz tworzenia i wysyłki powiadomienia przez administratora.
     */
    public function create(): View
    {
        $school = Auth::user()->activeSchool();
        if (!$school) {
            abort(403, 'Brak aktywnej placówki.');
        }

        $courses = Course::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        $users = $school->users()
            ->orderBy('name')
            ->get();

        $templates = $this->notificationService->getQuickTemplates();

        return view('admin.notifications.create', compact('courses', 'users', 'templates'));
    }

    /**
     * Wysłanie powiadomienia do wybranej grupy odbiorców.
     */
    public function store(SendNotificationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sentCount = $this->notificationService->sendNotification(Auth::user(), $validated);

        if ($sentCount === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nie znaleziono żadnych odbiorców dla wybranego kryterium.');
        }

        return redirect()->route('admin.notifications.create')
            ->with('success', "Powiadomienie zostało pomyślnie wysłane do {$sentCount} odbiorców.");
    }

    /**
     * Masowa wysyłka podsumowania rozliczeń miesiąca do rodziców z poziomu modułu rozliczeń.
     */
    public function sendBillingSummary(Request $request): RedirectResponse
    {
        $request->validate([
            'year_month'   => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'target_group' => ['required', 'string', 'in:all,unpaid'],
            'custom_note'  => ['nullable', 'string', 'max:500'],
        ], [
            'year_month.required'   => 'Podaj okres rozliczeniowy (RRRR-MM).',
            'year_month.regex'      => 'Nieprawidłowy format okresu (wymagany format RRRR-MM).',
            'target_group.required' => 'Wybierz grupę docelową rozliczenia.',
            'target_group.in'       => 'Niepoprawna grupa docelowa.',
        ]);

        $yearMonth = (string) $request->input('year_month');
        $targetGroup = (string) $request->input('target_group');
        $customNote = $request->filled('custom_note') ? (string) $request->input('custom_note') : null;

        $sentCount = $this->notificationService->sendMonthlyBillingSummary(
            Auth::user(),
            $yearMonth,
            $targetGroup,
            $customNote
        );

        if ($sentCount === 0) {
            return redirect()->back()
                ->with('error', "Brak pozycji rozliczeniowych lub rodziców do powiadomienia za okres {$yearMonth}.");
        }

        return redirect()->back()
            ->with('success', "Wysłano powiadomienia z podsumowaniem rozliczeń za {$yearMonth} do {$sentCount} rodziców.");
    }
}
