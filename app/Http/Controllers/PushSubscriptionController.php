<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function __construct(
        private WebPushNotificationService $webPushService
    ) {}

    /**
     * Zwraca klucz publiczny VAPID dla przeglądarki.
     */
    public function key(): JsonResponse
    {
        return response()->json([
            'publicKey' => $this->webPushService->getPublicKey(),
        ]);
    }

    /**
     * Zapisuje lub aktualizuje subskrypcję Push przeglądarki dla zalogowanego użytkownika.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'content_encoding' => 'nullable|string',
        ]);

        $user = $request->user();
        $endpoint = $validated['endpoint'];
        $endpointHash = hash('sha256', $endpoint);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['content_encoding'] ?? 'aesgcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subskrypcja powiadomień Push została zarejestrowana.',
            'id' => $subscription->id,
        ]);
    }

    /**
     * Usuwa subskrypcję Push przeglądarki.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpointHash = hash('sha256', $validated['endpoint']);
        PushSubscription::where('endpoint_hash', $endpointHash)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subskrypcja powiadomień Push została wyrejestrowana.',
        ]);
    }

    /**
     * Wysyła powiadomienie testowe do aktualnie zalogowanego użytkownika.
     */
    public function test(Request $request): JsonResponse
    {
        $user = $request->user();
        $sentCount = $this->webPushService->sendToUser(
            user: $user,
            title: 'Test powiadomień Zajęciownia',
            body: 'To jest testowe powiadomienie Push z systemu Zajęciownia.pl!',
            url: route('dashboard', [], false)
        );

        return response()->json([
            'success' => true,
            'sent_count' => $sentCount,
            'message' => $sentCount > 0
                ? "Pomyślnie wysłano powiadomienie testowe na {$sentCount} urządzeń."
                : 'Nie znaleziono aktywnych urządzeń z subskrypcją dla Twojego konta.',
        ]);
    }
}
