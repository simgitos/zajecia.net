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
     * Oblicza i zapisuje/aktualizuje pozycje rozliczeniowe (BillingItem) dla danego dziecka i kursu w miesiącu.
     * Dla zajęć ryczałtowych (monthly_flat) tworzy/aktualizuje 1 pozycję zbiorczą na miesiąc (lesson_id = null).
     * Dla zajęć rozliczanych za lekcję (per_lesson_*) tworzy osobną pozycję BillingItem dla każdej lekcji.
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
                // Jeżeli miesiąc jest wcześniejszy niż data dołączenia -> brak rozliczenia (usuwamy nieopłacone wpisy jeśli istnieją)
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

        // Obsługa w zależności od billing_type:
        // 1. Ryczałt miesięczny (monthly_flat) - 1 pozycja zbiorcza miesięczna (lesson_id = null)
        if ($course->billing_type === 'monthly_flat') {
            $childLessonsCount = 0;
            $presentCount = 0;
            $absentCount = 0;
            $excusedCount = 0;

            foreach ($lessons as $lesson) {
                $lessonDate = Carbon::parse($lesson->realized_at)->startOfDay();

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

            if ($childLessonsCount === 0) {
                $amount = 0.0;
                $calculationNote = 'Brak odbytych lekcji dla uczestnika od momentu zapisu w tym miesiącu';
            } elseif ($course->type === 'individual' && $excusedCount > 0 && $presentCount === 0 && $absentCount === 0) {
                $amount = 0.0;
                $calculationNote = 'Zajęcia indywidualne usprawiedliwione (bez opłaty)';
            } else {
                $amount = (float) $course->price_per_unit;
                $calculationNote = "Stały ryczałt miesięczny (zrealizowano {$childLessonsCount} zajęć od zapisu)";
            }

            $billingItem = BillingItem::firstOrNew([
                'child_id' => $child->id,
                'course_id' => $course->id,
                'year_month' => $yearMonth,
                'lesson_id' => null,
            ]);

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

        // 2. Opłata za każdą lekcję (per_lesson_monthly, per_lesson_single) - OSOBNA pozycja na każdą lekcję
        $lastItem = null;
        $activeLessonIds = [];

        foreach ($lessons as $lesson) {
            $lessonDate = Carbon::parse($lesson->realized_at)->startOfDay();

            if ($enrollmentStartOfDay && $lessonDate->lt($enrollmentStartOfDay)) {
                continue;
            }

            $childAttendance = $lesson->attendances->first();
            if (!$childAttendance) {
                continue;
            }

            $activeLessonIds[] = $lesson->id;

            $status = $childAttendance->status;
            $isBillable = in_array($status, ['present', 'absent']); // obecny lub nieobecny płatny, usprawiedliwiony bezpłatny
            $pricePerUnit = (float) $course->price_per_unit;
            $amount = $isBillable ? $pricePerUnit : 0.00;

            $billingItem = BillingItem::firstOrNew([
                'child_id' => $child->id,
                'lesson_id' => $lesson->id,
            ]);

            // Jeśli lekcja jest usprawiedliwiona (0 zł) i nie była opłacona, usuwamy pozycję
            if ($amount <= 0.00 && $billingItem->status !== 'paid') {
                if ($billingItem->exists) {
                    $billingItem->delete();
                }
                continue;
            }

            $formattedDate = Carbon::parse($lesson->realized_at)->format('d.m.Y');
            $statusLabel = match($status) {
                'present' => 'Obecność',
                'absent' => 'Nieobecność (płatna)',
                'excused' => 'Usprawiedliwienie (bezpłatne)',
                default => $status
            };

            $priceFormatted = number_format($pricePerUnit, 2, ',', ' ');
            $notes = "Lekcja z dnia {$formattedDate}: {$statusLabel}" . ($lesson->topic ? " (Temat: {$lesson->topic})" : "");

            $billingItem->school_id = $child->school_id;
            $billingItem->parent_id = $child->parent_id;
            $billingItem->course_id = $course->id;
            $billingItem->year_month = $yearMonth;
            $billingItem->billing_type = $course->billing_type;
            $billingItem->price_per_unit = $pricePerUnit;
            $billingItem->realized_lessons_count = 1;
            $billingItem->present_count = $status === 'present' ? 1 : 0;
            $billingItem->absent_count = $status === 'absent' ? 1 : 0;
            $billingItem->excused_count = $status === 'excused' ? 1 : 0;
            $billingItem->amount = $amount;
            $billingItem->notes = $notes;

            if (!$billingItem->exists) {
                $billingItem->status = 'unpaid';
            }

            $billingItem->save();
            $lastItem = $billingItem;
        }

        // Usuwamy ewentualne stare nieopłacone wpisy dla lekcji, które zostały usunięte w tym miesiącu
        BillingItem::where('child_id', $child->id)
            ->where('course_id', $course->id)
            ->where('year_month', $yearMonth)
            ->whereNotNull('lesson_id')
            ->whereNotIn('lesson_id', $activeLessonIds)
            ->where('status', 'unpaid')
            ->delete();

        // Usuwamy ewentualny stary nieopłacony wpis zbiorczy (lesson_id = null), jeśli kiedyś kurs był ryczałtem
        BillingItem::where('child_id', $child->id)
            ->where('course_id', $course->id)
            ->where('year_month', $yearMonth)
            ->whereNull('lesson_id')
            ->where('status', 'unpaid')
            ->delete();

        return $lastItem;
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
