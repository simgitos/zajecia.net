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

        $isTpayConfigured = app(\App\Services\TpayPaymentService::class)->isSchoolConfigured($user->school);
        $commissionPercent = (float) \App\Models\Setting::get('online_payment_commission_percent', 1.5);

        return view('user.payments.index', compact(
            'childUnpaidSummaries',
            'totalUnpaidAmount',
            'totalPaidAmount',
            'paidItems',
            'children',
            'isTpayConfigured',
            'commissionPercent'
        ));
    }

    /**
     * Realizuje opłacenie pojedynczej pozycji rozliczeniowej online (Tpay).
     */
    public function payItem(BillingItem $item, \App\Services\TpayPaymentService $tpayService)
    {
        $user = Auth::user();

        if ($item->parent_id !== $user->id || $item->school_id !== $user->school_id) {
            abort(403, 'Brak dostępu do tej pozycji rozliczeniowej.');
        }

        if ($item->status === 'paid') {
            return redirect()->route('user.payments.index')
                ->with('info', 'Ta pozycja została już wcześniej opłacona.');
        }

        try {
            $payment = $tpayService->createPaymentForBillingItems($user, $item);

            if ($payment->payment_url) {
                return redirect()->away($payment->payment_url);
            }

            return redirect()->route('user.payments.index')
                ->with('error', 'Nie udało się uzyskać linku do płatności Tpay.');
        } catch (\Throwable $e) {
            return redirect()->route('user.payments.index')
                ->with('error', 'Wystąpił błąd podczas inicjalizacji płatności Tpay: ' . $e->getMessage());
        }
    }

    /**
     * Realizuje opłacenie wszystkich zaległych pozycji rozliczeniowych rodzica online (Tpay).
     */
    public function payAll(\App\Services\TpayPaymentService $tpayService)
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

        try {
            $payment = $tpayService->createPaymentForBillingItems($user, $unpaidItems);

            if ($payment->payment_url) {
                return redirect()->away($payment->payment_url);
            }

            return redirect()->route('user.payments.index')
                ->with('error', 'Nie udało się uzyskać linku do płatności Tpay.');
        } catch (\Throwable $e) {
            return redirect()->route('user.payments.index')
                ->with('error', 'Wystąpił błąd podczas inicjalizacji płatności Tpay: ' . $e->getMessage());
        }
    }

    /**
     * Powrót po udanej płatności z Tpay.
     */
    public function success(Request $request): \Illuminate\Http\RedirectResponse
    {
        $session = $request->query('session');
        if ($session) {
            $payment = \App\Models\Payment::where('session_id', $session)
                ->where('user_id', Auth::id())
                ->first();

            if ($payment) {
                return redirect()->route('user.payments.index')
                    ->with('success', 'Dziękujemy! Płatność została przekazana do realizacji. Trwa księgowanie wpłaty.');
            }
        }

        return redirect()->route('user.payments.index')
            ->with('success', 'Płatność została zakończona. Po otrzymaniu potwierdzenia od operatora status zajęć zostanie zaktualizowany.');
    }

    /**
     * Powrót w przypadku anulowania lub błędu płatności w Tpay.
     */
    public function error(Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('user.payments.index')
            ->with('error', 'Płatność online została anulowana lub wystąpił błąd podczas przetwarzania transakcji.');
    }
}

