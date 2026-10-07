<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillingController extends Controller
{
    private array $polishMonths = [
        1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
        5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
        9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
    ];

    /**
     * Lista rozliczeń i pozycji płatności w szkole dla administratora.
     */
    public function index(Request $request): View
    {
        $schoolId = Auth::user()->school_id;

        // Domyślnie wybrany bieżący miesiąc, chyba że użytkownik wybierze konkretny miesiąc lub "all"
        $month = $request->input('month', now()->format('Y-m'));

        $query = BillingItem::where('school_id', $schoolId)
            ->where('amount', '>', 0)
            ->with(['child', 'parent', 'course']);

        $statsQuery = BillingItem::where('school_id', $schoolId)->where('amount', '>', 0);

        if ($month !== 'all' && $month !== '') {
            $query->where('year_month', $month);
            $statsQuery->where('year_month', $month);

            try {
                $selectedDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $selectedMonthName = ($this->polishMonths[$selectedDate->month] ?? '') . ' ' . $selectedDate->year;
            } catch (\Exception $e) {
                $selectedMonthName = $month;
            }
        } else {
            $selectedMonthName = 'Wszystkie okresy';
        }

        $statusFilter = $request->input('status', '');
        if (in_array($statusFilter, ['paid', 'unpaid'])) {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('child', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                })->orWhereHas('parent', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('course', function ($cq) use ($search) {
                    $cq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $billingItems = $query->latest()->paginate(20);

        // Ogólne podsumowanie finansowe szkoły dla wybranego filtru okresu
        $totalAmount = (float) (clone $statsQuery)->sum('amount');
        $totalPaid = (float) (clone $statsQuery)->where('status', 'paid')->sum('amount');
        $totalUnpaid = (float) (clone $statsQuery)->where('status', 'unpaid')->sum('amount');

        // Opcje wyboru miesiąca (Wszystkie okresy + Ostatnie 12 miesięcy)
        $monthOptions = [
            'all' => 'Wszystkie okresy (Rozliczenia ogółem)',
        ];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $key = $d->format('Y-m');
            $label = ($this->polishMonths[$d->month] ?? $d->format('F')) . ' ' . $d->year;
            $monthOptions[$key] = $label;
        }

        return view('admin.billing.index', compact(
            'billingItems',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'month',
            'selectedMonthName',
            'monthOptions',
            'statusFilter'
        ));
    }

    /**
     * Oznaczanie pozycji płatności jako opłaconej przez administratora.
     */
    public function markPaid(Request $request, BillingItem $item, BillingService $service): RedirectResponse
    {
        if ($item->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $paymentMethod = $request->input('payment_method', 'transfer');
        $transactionId = $request->input('transaction_id');

        $service->markAsPaid($item, $paymentMethod, $transactionId);

        return redirect()->back()
            ->with('success', 'Pozycja rozliczeniowa została oznaczona jako opłacona.');
    }

    /**
     * Oznaczanie pozycji jako nieopłaconej.
     */
    public function markUnpaid(BillingItem $item, BillingService $service): RedirectResponse
    {
        if ($item->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $service->markAsUnpaid($item);

        return redirect()->back()
            ->with('success', 'Status pozycji zmieniony na nieopłacony.');
    }
}
