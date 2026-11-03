<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use ErrorException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotificationService
{
    private ?WebPush $webPush = null;

    public function __construct()
    {
        $publicKey = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');
        $subject = config('services.vapid.subject', 'mailto:kontakt@zajeciownia.pl');

        if ($publicKey && $privateKey) {
            $auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ];

            // W środowisku Windows upewniamy się, że OpenSSL ma poprawną ścieżkę do openssl.cnf jeśli nie jest zdefiniowana
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !getenv('OPENSSL_CONF')) {
                $possiblePaths = [
                    'C:\\xampp\\apache\\conf\\openssl.cnf',
                    'C:\\xampp\\php\\extras\\ssl\\openssl.cnf',
                    'C:\\xampp\\php\\extras\\openssl\\openssl.cnf',
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        putenv("OPENSSL_CONF={$path}");
                        break;
                    }
                }
            }

            try {
                $this->webPush = new WebPush($auth);
                $this->webPush->setReuseVAPIDHeaders(true);
            } catch (\Throwable $e) {
                Log::error('WebPush initialization error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Zwraca klucz publiczny VAPID do subskrypcji w przeglądarce.
     */
    public function getPublicKey(): ?string
    {
        return config('services.vapid.public_key');
    }

    /**
     * Wysyła powiadomienie Push do pojedynczego użytkownika (na wszystkie zarejestrowane urządzenia).
     */
    public function sendToUser(User $user, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        $subscriptions = $user->pushSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        return $this->sendToSubscriptions($subscriptions, $title, $body, $url, $extra);
    }

    /**
     * Wysyła powiadomienie Push do wielu użytkowników.
     *
     * @param Collection<int, User>|array<int, User> $users
     */
    public function sendToUsers(Collection|array $users, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        $userIds = collect($users)->pluck('id')->filter()->all();
        if (empty($userIds)) {
            return 0;
        }

        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        return $this->sendToSubscriptions($subscriptions, $title, $body, $url, $extra);
    }

    /**
     * Wysyła powiadomienie do kolekcji rekordów PushSubscription.
     *
     * @param Collection<int, PushSubscription> $subscriptions
     */
    public function sendToSubscriptions(Collection $subscriptions, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        if (!$this->webPush) {
            Log::warning('WebPush is not configured. Missing VAPID keys.');
            return 0;
        }

        $payload = json_encode(array_merge([
            'title' => $title,
            'body' => $body,
            'icon' => '/icons/icon-192x192.png',
            'badge' => '/icons/icon-192x192.png',
            'data' => [
                'url' => $url ?: '/pulpit',
            ],
        ], $extra));

        $queuedCount = 0;
        $subscriptionMap = [];

        foreach ($subscriptions as $sub) {
            try {
                $webPushSubscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                ]);

                $this->webPush->queueNotification($webPushSubscription, $payload);
                $queuedCount++;
                $subscriptionMap[$sub->endpoint] = $sub;
            } catch (\Throwable $e) {
                Log::warning("Error queuing push for subscription {$sub->id}: " . $e->getMessage());
            }
        }

        if ($queuedCount === 0) {
            return 0;
        }

        $sentSuccessCount = 0;

        try {
            /** @var \Minishlink\WebPush\MessageSentReport $report */
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getEndpoint();
                $isSuccess = $report->isSuccess();

                if ($isSuccess) {
                    $sentSuccessCount++;
                } else {
                    Log::warning("WebPush failed for {$endpoint}: " . $report->getReason());

                    // Jeśli endpoint wygasł lub subskrypcja jest nieważna (404 Not Found, 410 Gone), usuwamy z bazy
                    if ($report->isSubscriptionExpired()) {
                        if (isset($subscriptionMap[$endpoint])) {
                            $subscriptionMap[$endpoint]->delete();
                        } else {
                            PushSubscription::where('endpoint_hash', hash('sha256', $endpoint))->delete();
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error flushing web push notifications: ' . $e->getMessage());
        }

        return $sentSuccessCount;
    }
}
