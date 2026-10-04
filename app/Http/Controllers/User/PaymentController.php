<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Models\Child;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Nazwy miesięcy w języku polskim.
     */
    private array $polishMonths = [
        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
    ];

    /**
     * Podsumowanie płatności dla rodzica na podstawie zapisanych pozycji rozliczeniowych.
     */
    public function index(Request $request, BillingService $billingService): View
    {
        $user = Auth::user();

        // Określenie wybranego miesiąca (format YYYY-MM, domyślnie bieżący)
        $month = $request->input('month', now()->format('Y-m'));
        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $selectedDate = now()->startOfMonth();
            $month = $selectedDate->format('Y-m');
        }

        // Synchronizujemy pozycje rozliczeniowe w bazie danych billing_items
        $billingService->syncParentMonth($user, $month);

        // Pobieramy zapisane w bazie pozycje rozliczeniowe dla tego rodzica i miesiąca
        $billingItems = BillingItem::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->where('year_month', $month)
            ->with(['child', 'course.instructor', 'course.room'])
            ->get();

        // Pobranie wszystkich dzieci rodzica do grupowania w widoku
        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->get();

        $totalAmount = 0.0;
        $totalPaidAmount = 0.0;
        $totalUnpaidAmount = 0.0;
        $totalCoursesCount = $billingItems->count();
        $childSummaries = [];

        foreach ($children as $child) {
            $childItems = $billingItems->where('child_id', $child->id);
            $childTotal = (float) $childItems->sum('amount');
            $childPaid = (float) $childItems->where('status', 'paid')->sum('amount');
            $childUnpaid = (float) $childItems->where('status', 'unpaid')->sum('amount');

            $totalAmount += $childTotal;
            $totalPaidAmount += $childPaid;
            $totalUnpaidAmount += $childUnpaid;

            $childSummaries[] = [
                'child' => $child,
                'child_total' => $childTotal,
                'child_paid' => $childPaid,
                'child_unpaid' => $childUnpaid,
                'items' => $childItems,
            ];
        }

        // Opcje wyboru miesiąca (ostatnie 12 miesięcy)
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
            'totalPaidAmount',
            'totalUnpaidAmount',
            'totalCoursesCount',
            'month',
            'selectedMonthName',
            'monthOptions'
        ));
    }
}
