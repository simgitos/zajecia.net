<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BillingItem;
use App\Models\Child;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;

class BillingService
{
    /**
     * Oblicza i zapisuje/aktualizuje pozycję rozliczeniową (BillingItem) dla danego dziecka i kursu w miesiącu.
     * Dolicza płatność jedynie od momentu zapisu i usuwa/nie tworzy pozycji o kwocie 0 zł.
     */
    public function syncBillingItem(Child $child, Course $course, string $yearMonth): ?BillingItem
    {
        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        } catch (\Exception $e) {
            $selectedDate = now()->startOfMonth();
            $yearMonth = $selectedDate->format('Y-m');
        }

        // Sprawdzamy datę dołączenia dziecka do zajęć (pivot child_course lub data rejestracji dziecka)
        $enrollmentTimestamp = $course->pivot?->created_at ?? $child->created_at;
        if ($enrollmentTimestamp) {
            $enrollmentMonth = Carbon::parse($enrollmentTimestamp)->format('Y-m');
            if ($yearMonth < $enrollmentMonth) {
                // Jeżeli miesiąc jest wcześniejszy niż data dołączenia -> brak rozliczenia (usuwamy nieopłacony zerowy wpis jeśli istnieje)
                BillingItem::where('child_id', $child->id)
                    ->where('course_id', $course->id)
                    ->where('year_month', $yearMonth)
                    ->where('status', 'unpaid')
                    ->delete();

                return null;
            }
        }

        $enrollmentStartOfDay = $enrollmentTimestamp ? Carbon::parse($enrollmentTimestamp)->startOfDay() : null;

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();

        // Pobranie zrealizowanych lekcji dla tego kursu w tym miesiącu wraz z obecnościami dziecka
        $lessons = $course->lessons()
            ->whereBetween('realized_at', [$startDate, $endDate])
            ->with(['attendances' => function ($query) use ($child) {
                $query->where('child_id', $child->id);
            }])
            ->get();

        $childLessonsCount = 0;
        $presentCount = 0;
        $absentCount = 0;
        $excusedCount = 0;

        foreach ($lessons as $lesson) {
            $lessonDate = Carbon::parse($lesson->realized_at)->startOfDay();

            // Lekcja z datą sprzed momentu zapisu dziecka do kursu nie dotyczy tego dziecka
            if ($enrollmentStartOfDay && $lessonDate->lt($enrollmentStartOfDay)) {
                continue;
            }

            $childAttendance = $lesson->attendances->first();
            if ($childAttendance) {
                $childLessonsCount++;
                if ($childAttendance->status === 'present') {
                    $presentCount++;
                } elseif ($childAttendance->status === 'absent') {
                    $absentCount++;
                } elseif ($childAttendance->status === 'excused') {
                    $excusedCount++;
                }
            }
        }

        $amount = 0.0;
        $calculationNote = '';

        // Naliczenie opłaty (zarówno ryczałtu jak i opłat za lekcję) następuje dopiero po odbyciu zajęć przez uczestnika
        if ($childLessonsCount === 0) {
            $amount = 0.0;
            $calculationNote = 'Brak odbytych lekcji dla uczestnika od momentu zapisu w tym miesiącu';
        } else {
            switch ($course->billing_type) {
                case 'monthly_flat':
                    if ($course->type === 'individual' && $excusedCount > 0 && $presentCount === 0 && $absentCount === 0) {
                        $amount = 0.0;
                        $calculationNote = 'Zajęcia indywidualne usprawiedliwione (bez opłaty)';
                    } else {
                        $amount = (float) $course->price_per_unit;
                        $calculationNote = "Stały ryczałt miesięczny (zrealizowano {$childLessonsCount} zajęć od zapisu)";
                    }
                    break;

                case 'per_lesson_monthly':
                case 'per_lesson_single':
                    // Zgodnie z zasadami: obecny i nieobecny dolicza kwotę do billingu, usprawiedliwiony nie dolicza
                    $billableCount = $presentCount + $absentCount;

                    $amount = (float) $course->price_per_unit * $billableCount;
                    $priceFormatted = number_format((float) $course->price_per_unit, 2, ',', ' ');

                    $parts = [];
                    $parts[] = "{$presentCount} obecn.";
                    if ($absentCount > 0) {
                        $parts[] = "{$absentCount} nieobecn. (płatne)";
                    }
                    if ($excusedCount > 0) {
                        $parts[] = "{$excusedCount} uspr. (bezpłatne)";
                    }

                    $details = implode(', ', $parts);
                    $calculationNote = "{$billableCount} płatnych lekcji ({$details}) × {$priceFormatted} zł";
                    break;
            }
        }

        $billingItem = BillingItem::firstOrNew([
            'child_id' => $child->id,
            'course_id' => $course->id,
            'year_month' => $yearMonth,
        ]);

        // Jeżeli kwota wynosi 0 zł i pozycja nie jest jeszcze opłacona -> usuwamy nieopłacony wpis 0 zł
        if ($amount <= 0.00 && $billingItem->status !== 'paid') {
            if ($billingItem->exists) {
                $billingItem->delete();
            }
            return null;
        }

        $billingItem->school_id = $child->school_id;
        $billingItem->parent_id = $child->parent_id;
        $billingItem->billing_type = $course->billing_type;
        $billingItem->price_per_unit = (float) $course->price_per_unit;
        $billingItem->realized_lessons_count = $childLessonsCount;
        $billingItem->present_count = $presentCount;
        $billingItem->excused_count = $excusedCount;
        $billingItem->absent_count = $absentCount;
        $billingItem->amount = $amount;
        $billingItem->notes = $calculationNote;

        if (!$billingItem->exists) {
            $billingItem->status = 'unpaid';
        }

        $billingItem->save();

        return $billingItem;
    }

    /**
     * Synchronizuje pozycje rozliczeniowe dla wszystkich zapisanych dzieci w kursie w danym miesiącu.
     */
    public function syncCourseMonth(Course $course, string $yearMonth): void
    {
        $course->loadMissing('children');
        foreach ($course->children as $child) {
            $this->syncBillingItem($child, $course, $yearMonth);
        }
    }

    /**
     * Synchronizuje pozycje rozliczeniowe dla wszystkich dzieci danego rodzica
     * od momentu dołączenia do zajęć aż do bieżącego miesiąca.
     */
    public function syncParentAll(User $parent): void
    {
        $children = Child::where('parent_id', $parent->id)
            ->where('school_id', $parent->school_id)
            ->with(['courses' => function ($query) {
                $query->withPivot('created_at');
            }])
            ->get();

        $currentMonthDate = now()->startOfMonth();

        foreach ($children as $child) {
            foreach ($child->courses as $course) {
                $enrollmentTimestamp = $course->pivot?->created_at ?? $child->created_at;
                $startDate = $enrollmentTimestamp ? Carbon::parse($enrollmentTimestamp)->startOfMonth() : $currentMonthDate->copy();

                $tempDate = $startDate->copy();
                while ($tempDate->lte($currentMonthDate)) {
                    $yearMonth = $tempDate->format('Y-m');
                    $this->syncBillingItem($child, $course, $yearMonth);
                    $tempDate->addMonth();
                }
            }
        }
    }

    /**
     * Oznacza pozycję rozliczeniową jako opłaconą.
     */
    public function markAsPaid(BillingItem $item, string $paymentMethod = 'transfer', ?string $transactionId = null): void
    {
        $item->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Oznacza pozycję rozliczeniową jako nieopłaconą.
     */
    public function markAsUnpaid(BillingItem $item): void
    {
        $item->update([
            'status' => 'unpaid',
            'paid_at' => null,
            'payment_method' => null,
            'transaction_id' => null,
        ]);
    }

    /**
     * Przelicza wszystkie pozycje rozliczeniowe w szkole lub globalnie dla nieopłaconych wpisów.
     */
    public function recalculateAllUnpaid(?int $schoolId = null): int
    {
        $query = BillingItem::where('status', 'unpaid')->with(['child', 'course']);
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $items = $query->get();
        $count = 0;

        foreach ($items as $item) {
            if ($item->child && $item->course) {
                $this->syncBillingItem($item->child, $item->course, $item->year_month);
                $count++;
            }
        }

        return $count;
    }
}
