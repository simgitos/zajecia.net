<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TpayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TpayWebhookController extends Controller
{
    /**
     * Odbiera i weryfikuje powiadomienia JWS od Tpay.
     */
    public function __invoke(Request $request, TpayPaymentService $tpayPaymentService): Response
    {
        try {
            $tpayPaymentService->handleWebhook();
            return response('TRUE', 200)->header('Content-Type', 'text/plain');
        } catch (\Throwable $e) {
            Log::error('Błąd podczas przetwarzania webhooka Tpay: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all(),
            ]);

            return response('FALSE - ' . $e->getMessage(), 400)->header('Content-Type', 'text/plain');
        }
    }
}
