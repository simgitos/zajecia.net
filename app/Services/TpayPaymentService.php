<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BillingItem;
use App\Models\Payment;
use App\Models\School;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Illuminate\Support\Facades\Http;
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
     * Sprawdza, czy globalne płatności online Tpay są skonfigurowane w systemie.
     */
    public function isGlobalConfigured(): bool
    {
        return Setting::isTpayConfigured();
    }

    /**
     * Sprawdza, czy dla danej szkoły płatności online są aktywne.
     * Wymagany jest globalny Tpay oraz podany numer rachunku bankowego szkoły.
     */
    public function isSchoolConfigured(?School $school): bool
    {
        if (!$this->isGlobalConfigured()) {
            return false;
        }

        if (!$school) {
            return false;
        }

        // Szkoła musi posiadać rachunek bankowy
        return !empty($school->bank_account_number);
    }

    /**
     * Inicjalizuje instancję TpayApi na podstawie globalnych poświadczeń z tabeli settings.
     */
    public function getApiClient(): TpayApi
    {
        $clientId = (string) Setting::get('tpay_client_id');
        $clientSecret = (string) Setting::getSecret('tpay_client_secret');
        $productionMode = (bool) Setting::get('tpay_production_mode', false);

        if (empty($clientId) || empty($clientSecret)) {
            throw new RuntimeException('Globalne poświadczenia operatora Tpay nie zostały jeszcze skonfigurowane w panelu SuperAdmina (SU).');
        }

        $psrCache = $this->createSimpleCacheAdapter();
        $tpayCache = new TpayCache(null, $psrCache);

        return new TpayApi(
            $tpayCache,
            $clientId,
            $clientSecret,
            $productionMode
        );
    }

    /**
     * Kompatybilność wsteczna - zwraca instancję API dla szkoły (lub globalną).
     */
    public function getApiClientForSchool(?School $school = null): TpayApi
    {
        return $this->getApiClient();
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
            ? (School::find($firstItem->school_id) ?? $user->school)
            : $user->school;

        if (!$school) {
            throw new RuntimeException('Użytkownik nie jest przypisany do żadnej szkoły.');
        }

        if (!$this->isGlobalConfigured()) {
            throw new RuntimeException('Płatności online nie są obecnie skonfigurowane w systemie. Skontaktuj się z administratorem.');
        }

        if (empty($school->bank_account_number)) {
            throw new RuntimeException("Placówka \"{$school->name}\" nie posiada zdefiniowanego numeru konta bankowego do wypłat.");
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

        $tpayApi = $this->getApiClient();

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

        // Obsługa Tpay Collect - bezpośrednie przekazanie środków na subkonto/rachunek placówki
        $iban = $school->formattedIban();
        if ($iban) {
            $receiverName = preg_replace('/[^A-Za-z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ \/\-,\.]/u', '', (string) ($school->full_name ?: $school->name));
            $transactionData['collect'] = [
                'account' => $iban,
                'receiver' => mb_substr(trim($receiverName), 0, 100),
            ];
        }

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
     * Dodaje rachunek bankowy szkoły do białej listy Tpay Collect (POST /collect/bank-accounts).
     */
    public function addToWhitelist(School $school): array
    {
        $iban = $school->formattedIban();
        if (!$iban) {
            throw new RuntimeException('Placówka nie posiada wprowadzonego poprawnego numeru konta bankowego.');
        }

        $clientId = (string) Setting::get('tpay_client_id');
        $clientSecret = (string) Setting::getSecret('tpay_client_secret');
        $productionMode = (bool) Setting::get('tpay_production_mode', false);

        if (empty($clientId) || empty($clientSecret)) {
            throw new RuntimeException('Brak skonfigurowanych globalnych poświadczeń Tpay w ustawieniach SU.');
        }

        $oauthBaseUrl = $productionMode ? 'https://api.tpay.com' : 'https://openapi.sandbox.tpay.com';
        $apiBaseUrl = $productionMode ? 'https:api.tpay.com' : 'https://openapi.sandbox.tpay.com'; // lub sandbox

        // 1. Pobranie Bearer Token z OAuth
        $authResponse = Http::asJson()->timeout(15)->post("{$oauthBaseUrl}/oauth/auth", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$authResponse->successful()) {
            throw new RuntimeException('Błąd autoryzacji w Tpay OAuth: ' . ($authResponse->json('error_description') ?? $authResponse->body()));
        }

        $token = $authResponse->json('access_token');
        if (empty($token)) {
            throw new RuntimeException('Nie udało się uzyskać tokena autoryzacyjnego z Tpay.');
        }

        // 2. Dodanie konta do białej listy Tpay Collect
        $ownerName = mb_substr((string) ($school->full_name ?: $school->name), 0, 1000);
        $additionalInfo = mb_substr(trim("NIP: {$school->nip}, Adres: {$school->address}, {$school->postal_code} {$school->city}"), 0, 1000);

        $payload = [
            'accountNumber' => $iban,
            'ownerName' => $ownerName,
            'additionalInformation' => $additionalInfo,
        ];

        $whitelistResponse = Http::withToken($token)
            ->asJson()
            ->timeout(15)
            ->post("{$apiBaseUrl}/collect/bank-accounts", $payload);

        $responseData = $whitelistResponse->json() ?? ['raw' => $whitelistResponse->body()];

        if ($whitelistResponse->successful() || $whitelistResponse->status() === 201 || $whitelistResponse->status() === 200) {
            $school->update([
                'tpay_whitelist_status' => 'verified',
                'tpay_whitelist_at' => now(),
                'tpay_whitelist_response' => json_encode($responseData),
            ]);

            return [
                'success' => true,
                'message' => 'Rachunek bankowy placówki został pomyślnie zgłoszony i zweryfikowany na białej liście Tpay Collect.',
                'data' => $responseData,
            ];
        }

        // Jeśli konto już istnieje w białej liście
        $body = $whitelistResponse->body();
        if (str_contains(strtolower($body), 'already') || str_contains(strtolower($body), 'istnieje')) {
            $school->update([
                'tpay_whitelist_status' => 'verified',
                'tpay_whitelist_at' => now(),
                'tpay_whitelist_response' => json_encode($responseData),
            ]);

            return [
                'success' => true,
                'message' => 'Rachunek bankowy placówki znajduje się już na białej liście Tpay.',
                'data' => $responseData,
            ];
        }

        throw new RuntimeException('Odpowiedź Tpay API (' . $whitelistResponse->status() . '): ' . $body);
    }

    /**
     * Weryfikuje i przetwarza webhook z powiadomieniem od Tpay.
     */
    public function handleWebhook(): void
    {
        $requestParser = new RequestParser();
        $parsed = $requestParser->getParsedContent();

        $hiddenDescription = $parsed['hiddenDescription'] ?? $parsed['tr_crc'] ?? null;
        $tpayTransactionId = $parsed['transactionId'] ?? $parsed['tr_id'] ?? null;
        $status = strtolower((string) ($parsed['status'] ?? $parsed['tr_status'] ?? ''));

        // Wyszukanie płatności w bazie danych
        $payment = null;
        if (!empty($hiddenDescription)) {
            $payment = Payment::where('session_id', $hiddenDescription)->with('school')->first();
        }

        if (!$payment && !empty($tpayTransactionId)) {
            $payment = Payment::where('tpay_transaction_id', $tpayTransactionId)->with('school')->first();
        }

        if (!$payment || !$payment->school) {
            Log::warning('Tpay Webhook: Nie znaleziono płatności w bazie danych', [
                'hiddenDescription' => $hiddenDescription,
                'tpayTransactionId' => $tpayTransactionId,
            ]);
            throw new RuntimeException('Nie znaleziono zarejestrowanej płatności dla powiadomienia.');
        }

        $merchantSecret = (string) (Setting::getSecret('tpay_merchant_secret') ?: Setting::getSecret('tpay_client_secret'));
        $productionMode = (bool) Setting::get('tpay_production_mode', false);

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

        $notificationService->getNotification();

        Log::info("Odebrano zweryfikowane powiadomienie Tpay dla płatności #{$payment->id}", [
            'payment_id' => $payment->id,
            'session_id' => $payment->session_id,
            'status' => $status,
        ]);

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
