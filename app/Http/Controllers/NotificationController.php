<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Wyświetla pełną listę powiadomień zalogowanego użytkownika.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Oznacza pojedyncze powiadomienie jako przeczytane i opcjonalnie przekierowuje do przypisanego adresu URL.
     */
    public function read(Request $request, string $id): RedirectResponse|JsonResponse
    {
        /** @var DatabaseNotification|null $notification */
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->unreadNotifications()->count(),
            ]);
        }

        $url = $notification?->data['url'] ?? null;
        if (!empty($url)) {
            return redirect($url);
        }

        return redirect()->back()->with('success', 'Powiadomienie zostało oznaczone jako przeczytane.');
    }

    /**
     * Oznacza wszystkie powiadomienia zalogowanego użytkownika jako przeczytane.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Wszystkie powiadomienia zostały oznaczone jako przeczytane.');
    }

    /**
     * Zwraca liczbę nieprzeczytanych powiadomień w formacie JSON.
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
