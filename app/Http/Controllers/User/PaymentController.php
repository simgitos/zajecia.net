<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Miesiące w języku polskim.
     */
    private array $polishMonths = [
        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
    ];

    /**
     * Podsumowanie płatności dla rodzica za zajęcia dzieci.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Określenie wybranego miesiąca (format Y-m, domyślnie bieżący)
        $month = $request->input('month', now()->format('Y-m'));
        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $selectedDate = now()->startOfMonth();
            $month = $selectedDate->format('Y-m');
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();

        // Pobranie dzieci z relacją zajęć oraz zrealizowanych lekcji w danym miesiącu
        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->with(['courses' => function ($query) use ($startDate, $endDate) {
                $query->with(['lessons' => function ($lq) use ($startDate, $endDate) {
                    $lq->whereBetween('realized_at', [$startDate, $endDate])
                       ->with('attendances');
                }]);
            }])
            ->get();

        $totalAmount = 0.0;
        $totalCoursesCount = 0;
        $childSummaries = [];

        foreach ($children as $child) {
            $childTotal = 0.0;
            $courseDetails = [];

            foreach ($child->courses as $course) {
                $totalCoursesCount++;
                $realizedLessonsCount = 0;
                $presentCount = 0;
                $absentCount = 0;
                $excusedCount = 0;

                foreach ($course->lessons as $lesson) {
                    $realizedLessonsCount++;
                    $childAttendance = $lesson->attendances->firstWhere('child_id', $child->id);
                    if ($childAttendance) {
                        if ($childAttendance->status === 'present') {
                            $presentCount++;
                        } elseif ($childAttendance->status === 'absent') {
                            $absentCount++;
                        } elseif ($childAttendance->status === 'excused') {
                            $excusedCount++;
                        }
                    }
                }

                $dueAmount = 0.0;
                $calculationNote = '';

                // Doliczaj do płatności JEDYNIE w przypadku gdy lekcja w danym miesiącu się odbyła
                if ($realizedLessonsCount === 0) {
                    $dueAmount = 0.0;
                    $calculationNote = 'Brak przeprowadzonych lekcji w tym miesiącu';
                } else {
                    switch ($course->billing_type) {
                        case 'monthly_flat':
                            // Ryczałt naliczamy jeśli odbyła się lekcja w miesiącu.
                            // W przypadku zajęć indywidualnych, jeśli wszystkie były usprawiedliwione - nie doliczamy.
                            if ($course->type === 'individual' && $excusedCount > 0 && $presentCount === 0) {
                                $dueAmount = 0.0;
                                $calculationNote = 'Zajęcia indywidualne usprawiedliwione';
                            } else {
                                $dueAmount = (float) $course->price_per_unit;
                                $calculationNote = 'Stały ryczałt miesięczny';
                            }
                            break;

                        case 'per_lesson_monthly':
                        case 'per_lesson_single':
                            // Dla zajęć indywidualnych: usprawiedliwionych NIE doliczamy (tylko obecne / nieusprawiedliwione)
                            // Dla zajęć grupowych: zrealizowane lekcje pomniejszone o usprawiedliwione
                            if ($course->type === 'individual') {
                                $billableCount = $presentCount;
                            } else {
                                $billableCount = max(0, $realizedLessonsCount - $excusedCount);
                            }

                            $dueAmount = (float) $course->price_per_unit * $billableCount;
                            $priceFormatted = number_format((float) $course->price_per_unit, 2, ',', ' ');

                            if ($course->type === 'individual') {
                                $calculationNote = "{$billableCount} płatnych lekcji indywidualnych ({$presentCount} obecności, {$excusedCount} usprawiedliwionych)";
                            } else {
                                $calculationNote = "{$billableCount} płatnych lekcji ({$realizedLessonsCount} zrealizowanych, {$excusedCount} usprawiedliwionych) × {$priceFormatted} zł";
                            }
                            break;
                    }
                }

                $childTotal += $dueAmount;

                $courseDetails[] = [
                    'course' => $course,
                    'billing_type' => $course->billing_type,
                    'price_per_unit' => (float) $course->price_per_unit,
                    'realized_lessons_count' => $realizedLessonsCount,
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'excused_count' => $excusedCount,
                    'due_amount' => $dueAmount,
                    'calculation_note' => $calculationNote,
                ];
            }

            $totalAmount += $childTotal;

            $childSummaries[] = [
                'child' => $child,
                'child_total' => $childTotal,
                'courses' => $courseDetails,
            ];
        }

        // Generowanie listy miesięcy do wyboru (ostatnie 12 miesięcy)
        $monthOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $key = $d->format('Y-m');
            $label = ($this->polishMonths[$d->month] ?? $d->format('F')) . ' ' . $d->year;
            $monthOptions[$key] = $label;
        }

        $selectedMonthName = ($this->polishMonths[$selectedDate->month] ?? '') . ' ' . $selectedDate->year;

        return view('user.payments.index', compact(
            'childSummaries',
            'totalAmount',
            'totalCoursesCount',
            'month',
            'selectedMonthName',
            'monthOptions'
        ));
    }
}
