<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Models\Course;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Formularz wysyłki powiadomienia przez instruktora do jego grup i uczestników.
     */
    public function create(): View
    {
        $school = Auth::user()->activeSchool();
        if (!$school) {
            abort(403, 'Brak aktywnej placówki.');
        }

        // Tylko kursy prowadzone przez zalogowanego instruktora w danej szkole
        $courses = Course::where('school_id', $school->id)
            ->where('instructor_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        // Pobierz unikalnych rodziców dzieci zapisanych na kursy tego instruktora
        $courseIds = $courses->pluck('id');
        $users = \App\Models\Child::whereHas('courses', function ($q) use ($courseIds) {
                $q->whereIn('courses.id', $courseIds);
            })
            ->with('parent')
            ->get()
            ->pluck('parent')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        // Filtruj szablony adekwatne dla instruktora
        $allTemplates = $this->notificationService->getQuickTemplates();
        $templates = array_values(array_filter($allTemplates, fn ($t) => in_array($t['id'], [
            'cancellation', 'reschedule', 'organizational'
        ])));

        return view('teacher.notifications.create', compact('courses', 'users', 'templates'));
    }

    /**
     * Wysłanie powiadomienia przez instruktora.
     */
    public function store(SendNotificationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Nauczyciel może wysyłać tylko do grup lub pojedynczych osób
        if (in_array($validated['target_type'], ['instructors', 'all_parents'])) {
            abort(403, 'Brak uprawnień do wysyłki ogólnoszkolnej.');
        }

        $sentCount = $this->notificationService->sendNotification(Auth::user(), $validated);

        if ($sentCount === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nie znaleziono odbiorców dla wybranego kryterium.');
        }

        return redirect()->route('teacher.notifications.create')
            ->with('success', "Powiadomienie zostało pomyślnie wysłane do {$sentCount} odbiorców.");
    }
}
