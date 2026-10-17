<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BillingItem;
use App\Models\Payment;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Utilities\Cache as TpayCache;
use Tpay\OpenApi\Utilities\CacheCertificateProvider;
use Tpay\OpenApi\Utilities\RequestParser;
use Tpay\OpenApi\Webhook\JWSVerifiedPaymentNotification;

class TpayPaymentService
{
    /**
     * Sprawdza, czy dana szkoła posiada skonfigurowane dane Tpay.
     */
    public function isSchoolConfigured(?School $school): bool
    {
        if (!$school) {
            return false;
        }

        return !empty($school->tpay_client_id) && !empty($school->tpay_client_secret);
    }

    /**
     * Inicjalizuje instancję TpayApi dla podanej szkoły.
     */
    public function getApiClientForSchool(School $school): TpayApi
    {
        if (!$this->isSchoolConfigured($school)) {
            throw new RuntimeException("Szkoła \"{$school->name}\" nie posiada skonfigurowanych danych dostępowych Tpay (Client ID i Secret).");
        }

        $psrCache = $this->createSimpleCacheAdapter();
        $tpayCache = new TpayCache(null, $psrCache);

        return new TpayApi(
            $tpayCache,
            (string) $school->tpay_client_id,
            (string) $school->tpay_client_secret,
            (bool) $school->tpay_production_mode
        );
    }

    /**
     * Tworzy płatność Tpay dla kolekcji pozycji BillingItem lub pojedynczej.
     *
     * @param User $user
     * @param Collection<int, BillingItem>|BillingItem $items
     * @return Payment
     */
    public function createPaymentForBillingItems(User $user, Collection|BillingItem $items): Payment
    {
        $billingItems = $items instanceof BillingItem ? collect([$items]) : $items;

        if ($billingItems->isEmpty()) {
            throw new RuntimeException('Brak pozycji do opłacenia.');
        }

        $firstItem = $billingItems->first();
        $school = ($firstItem && $firstItem->school_id)
            ? (\App\Models\School::find($firstItem->school_id) ?? $user->school)
            : $user->school;

        if (!$school) {
            throw new RuntimeException('Użytkownik nie jest przypisany do żadnej szkoły.');
        }

        if (!$this->isSchoolConfigured($school)) {
            throw new RuntimeException('Płatności online nie są obecnie skonfigurowane dla tej placówki. Skontaktuj się z administratorem szkoły.');
        }

        $totalAmount = 0.0;
        foreach ($billingItems as $item) {
            if ($item->status === 'paid') {
                throw new RuntimeException("Pozycja #{$item->id} została już wcześniej opłacona.");
            }
            $totalAmount += (float) $item->amount;
        }

        if ($totalAmount <= 0) {
            throw new RuntimeException('Suma płatności musi być większa od zera.');
        }

        $sessionId = 'TPAY_' . strtoupper(Str::random(18));
        $title = $billingItems->count() === 1
            ? 'Opłata za zajęcia: ' . ($billingItems->first()->course?->title ?? 'Zajęcia')
            : 'Zbiorcza opłata za zajęcia (' . $billingItems->count() . ' pozycji)';

        $payment = Payment::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'title' => $title,
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        $payment->billingItems()->sync($billingItems->pluck('id'));

        // Pobieramy klienta Tpay dedykowanego dla tej konkretnej szkoły
        $tpayApi = $this->getApiClientForSchool($school);

        $transactionData = [
            'amount' => (float) number_format($totalAmount, 2, '.', ''),
            'description' => $title,
            'hiddenDescription' => $sessionId,
            'payer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => route('user.payments.success', ['session' => $sessionId]),
                    'error' => route('user.payments.error', ['session' => $sessionId]),
                ],
                'notification' => [
                    'url' => route('tpay.webhook'),
                ],
            ],
        ];

        try {
            $response = $tpayApi->transactions()->createTransaction($transactionData);
        } catch (\Throwable $e) {
            $payment->update([
                'status' => 'error',
                'payload' => ['error' => $e->getMessage()],
            ]);
            Log::error('Tpay createTransaction error: ' . $e->getMessage(), [
                'school_id' => $school->id,
                'payment_id' => $payment->id,
                'session_id' => $sessionId,
            ]);
            throw new RuntimeException('Nie udało się utworzyć transakcji w Tpay: ' . $e->getMessage());
        }

        $paymentUrl = $response['transactionPaymentUrl'] ?? $response['url'] ?? null;
        $transactionId = $response['transactionId'] ?? $response['title'] ?? null;

        $payment->update([
            'tpay_transaction_id' => $transactionId,
            'payment_url' => $paymentUrl,
            'payload' => $response,
        ]);

        return $payment;
    }

    /**
     * Weryfikuje i przetwarza webhook z powiadomieniem od Tpay dla odpowiedniej szkoły.
     */
    public function handleWebhook(): void
    {
        $requestParser = new RequestParser();
        $parsed = $requestParser->getParsedContent();

        $hiddenDescription = $parsed['hiddenDescription'] ?? $parsed['tr_crc'] ?? null;
        $tpayTransactionId = $parsed['transactionId'] ?? $parsed['tr_id'] ?? null;
        $status = strtolower((string) ($parsed['status'] ?? $parsed['tr_status'] ?? ''));

        // Wyszukanie płatności w bazie danych, aby poznać szkołę
        $payment = null;
        if (!empty($hiddenDescription)) {
            $payment = Payment::where('session_id', $hiddenDescription)->with('school')->first();
        }

        if (!$payment && !empty($tpayTransactionId)) {
            $payment = Payment::where('tpay_transaction_id', $tpayTransactionId)->with('school')->first();
        }

        if (!$payment || !$payment->school) {
            Log::warning('Tpay Webhook: Nie znaleziono płatności lub powiązanej szkoły w bazie danych', [
                'hiddenDescription' => $hiddenDescription,
                'tpayTransactionId' => $tpayTransactionId,
            ]);
            throw new RuntimeException('Nie znaleziono zarejestrowanej płatności dla powiadomienia.');
        }

        $school = $payment->school;
        $merchantSecret = (string) ($school->tpay_merchant_secret ?: $school->tpay_client_secret);
        $productionMode = (bool) $school->tpay_production_mode;

        // Weryfikacja JWS
        $psrCache = $this->createSimpleCacheAdapter();
        $tpayCache = new TpayCache(null, $psrCache);
        $certificateProvider = new CacheCertificateProvider($tpayCache);

        $notificationService = new JWSVerifiedPaymentNotification(
            $certificateProvider,
            $merchantSecret,
            $productionMode,
            $requestParser
        );

        // Jeśli sygnatura jest błędna, rzuci wyjątek TpayException
        $notificationService->getNotification();

        Log::info("Odebrano zweryfikowane powiadomienie Tpay dla szkoły #{$school->id} ({$school->name})", [
            'payment_id' => $payment->id,
            'session_id' => $payment->session_id,
            'status' => $status,
        ]);

        // Statusy w Tpay: 'correct', 'paid', 'success' oznaczają powodzenie
        if (in_array($status, ['correct', 'paid', 'true', 'success'], true)) {
            $this->finalizePaymentAsPaid($payment, (string) $tpayTransactionId);
        } elseif (in_array($status, ['error', 'chargeback', 'declined'], true)) {
            $payment->update([
                'status' => 'error',
                'payload' => array_merge((array) $payment->payload, ['webhook' => $parsed]),
            ]);
        }
    }

    /**
     * Oznacza płatność oraz przypisane billingItems jako opłacone.
     */
    public function finalizePaymentAsPaid(Payment $payment, ?string $transactionId = null): void
    {
        if ($payment->isPaid()) {
            return;
        }

        $billingService = app(BillingService::class);
        $txId = $transactionId ?? $payment->tpay_transaction_id ?? $payment->session_id;

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'tpay_transaction_id' => $txId,
        ]);

        foreach ($payment->billingItems as $item) {
            $billingService->markAsPaid($item, 'online', $txId);
        }

        Log::info("Płatność Payment #{$payment->id} ({$payment->session_id}) została oznaczona jako opłacona.", [
            'amount' => $payment->amount,
            'items_count' => $payment->billingItems()->count(),
        ]);
    }

    /**
     * Tworzy adapter CacheInterface kompatybilny z mechanizmem cache'owania w Laravelu.
     */
    private function createSimpleCacheAdapter(): CacheInterface
    {
        return new class implements CacheInterface {
            public function get(string $key, mixed $default = null): mixed {
                return LaravelCache::get($key, $default);
            }
            public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool {
                LaravelCache::put($key, $value, $ttl ?? 7200);
                return true;
            }
            public function delete(string $key): bool {
                return LaravelCache::forget($key);
            }
            public function clear(): bool {
                return LaravelCache::flush();
            }
            public function getMultiple(iterable $keys, mixed $default = null): iterable {
                return LaravelCache::many(is_array($keys) ? $keys : iterator_to_array($keys));
            }
            public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool {
                LaravelCache::putMany(is_array($values) ? $values : iterator_to_array($values), $ttl ?? 7200);
                return true;
            }
            public function deleteMultiple(iterable $keys): bool {
                foreach ($keys as $key) {
                    LaravelCache::forget($key);
                }
                return true;
            }
            public function has(string $key): bool {
                return LaravelCache::has($key);
            }
        };
    }
}
