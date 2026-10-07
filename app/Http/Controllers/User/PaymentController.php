<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Models\Child;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Nazwy miesięcy w języku polskim.
     */
    private array $polishMonths = [
        1 => 'Styczeń',
        2 => 'Luty',
        3 => 'Marzec',
        4 => 'Kwiecień',
        5 => 'Maj',
        6 => 'Czerwiec',
        7 => 'Lipiec',
        8 => 'Sierpień',
        9 => 'Wrzesień',
        10 => 'Październik',
        11 => 'Listopad',
        12 => 'Grudzień'
    ];

    /**
     * Podsumowanie płatności dla rodzica - rozliczenie skumulowane (bez podziału na miesiące).
     */
    public function index(Request $request, BillingService $billingService): View
    {
        $user = Auth::user();

        // Synchronizujemy rozliczenia dla wszystkich dzieci rodzica od momentu dołączenia do zajęć
        $billingService->syncParentAll($user);

        // Pobieramy wszystkie pozycje z kwotą > 0 zł dla tego rodzica
        $allBillingItems = BillingItem::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->where('amount', '>', 0)
            ->with(['child', 'course.instructor', 'course.room'])
            ->latest('year_month')
            ->get();

        // Podział na pozycje nieopłacone oraz opłacone
        $unpaidItems = $allBillingItems->where('status', 'unpaid');
        $paidItems = $allBillingItems->where('status', 'paid');

        $totalUnpaidAmount = (float) $unpaidItems->sum('amount');
        $totalPaidAmount = (float) $paidItems->sum('amount');

        // Pobranie wszystkich dzieci rodzica
        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->get();

        // Grupowanie nieopłaconych zajęć według dzieci
        $childUnpaidSummaries = [];
        foreach ($children as $child) {
            $childUnpaidItems = $unpaidItems->where('child_id', $child->id);
            $childUnpaidTotal = (float) $childUnpaidItems->sum('amount');

            $childUnpaidSummaries[] = [
                'child' => $child,
                'unpaid_total' => $childUnpaidTotal,
                'items' => $childUnpaidItems,
            ];
        }

        return view('user.payments.index', compact(
            'childUnpaidSummaries',
            'totalUnpaidAmount',
            'totalPaidAmount',
            'paidItems',
            'children'
        ));
    }

    /**
     * Realizuje opłacenie pojedynczej pozycji rozliczeniowej online.
     */
    public function payItem(BillingItem $item, BillingService $billingService)
    {
        $user = Auth::user();

        if ($item->parent_id !== $user->id || $item->school_id !== $user->school_id) {
            abort(403, 'Brak dostępu do tej pozycji rozliczeniowej.');
        }

        if ($item->status === 'paid') {
            return redirect()->route('user.payments.index')
                ->with('info', 'Ta pozycja została już wcześniej opłacona.');
        }

        $transactionId = 'PAY-ONL-' . strtoupper(Str::random(8));
        $billingService->markAsPaid($item, 'online', $transactionId);

        $courseTitle = $item->course?->title ?? 'Zajęcia';
        $amountFormatted = number_format((float) $item->amount, 2, ',', ' ');

        return redirect()->route('user.payments.index')
            ->with('success', "Płatność online w kwocie {$amountFormatted} zł za zajęcia \"{$courseTitle}\" została zrealizowana pomyślnie!");
    }

    /**
     * Realizuje opłacenie wszystkich zaległych pozycji rozliczeniowych rodzica online.
     */
    public function payAll(BillingService $billingService)
    {
        $user = Auth::user();

        $unpaidItems = BillingItem::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->where('status', 'unpaid')
            ->where('amount', '>', 0)
            ->get();

        if ($unpaidItems->isEmpty()) {
            return redirect()->route('user.payments.index')
                ->with('info', 'Brak nieopłaconych zajęć do rozliczenia.');
        }

        $totalPaid = 0.0;
        $count = $unpaidItems->count();
        $transactionId = 'PAY-ONL-BULK-' . strtoupper(Str::random(8));
        
        foreach ($unpaidItems as $item) {

            $billingService->markAsPaid($item, 'online', $transactionId);
            $totalPaid += (float) $item->amount;
        }

        $totalFormatted = number_format($totalPaid, 2, ',', ' ');
        return redirect()->route('user.payments.index')
            ->with('success', "Płatność online na sumę {$totalFormatted} zł za wszystkie nieopłacone pozycje ({$count}) została zrealizowana pomyślnie!");
    }
}

