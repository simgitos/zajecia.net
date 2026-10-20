<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Lista przeprowadzonych lekcji nauczyciela z filtrowaniem po miesiącu.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $filterMonth = (int) $request->get('month', now()->month);
        $filterYear  = (int) $request->get('year', now()->year);

        $recentLessons = Lesson::where('teacher_id', $user->id)
            ->where('school_id', $user->school_id)
            ->whereYear('realized_at', $filterYear)
            ->whereMonth('realized_at', $filterMonth)
            ->with(['course', 'attendances.child'])
            ->latest('realized_at')
            ->get();

        return view('teacher.lessons.index', compact('recentLessons', 'filterMonth', 'filterYear'));
    }

    /**
     * Formularz przeprowadzania / realizacji nowej lekcji.
     */
    public function create(Request $request, Course $course): View
    {
        $this->authorizeTeacherCourse($course);

        $course->load(['children' => function ($query) {
            $query->withPivot('created_at');
        }]);

        $selectedChildId = null;
        if ($course->isIndividual()) {
            $requestedChildId = $request->query('child_id') ? (int) $request->query('child_id') : null;
            if ($requestedChildId && $course->children->contains('id', $requestedChildId)) {
                $selectedChildId = $requestedChildId;
            } elseif ($course->children->isNotEmpty()) {
                $selectedChildId = $course->children->first()->id;
            }
        }

        return view('teacher.lessons.create', compact('course', 'selectedChildId'));
    }

    /**
     * Zapis realizacji lekcji wraz ze sprawdzoną listą obecności.
     */
    public function store(Request $request, Course $course, \App\Services\BillingService $billingService): RedirectResponse
    {
        $this->authorizeTeacherCourse($course);

        $realizedAtInput = $request->input('realized_at');
        $realizedAt = !empty($realizedAtInput) ? \Carbon\Carbon::parse($realizedAtInput)->startOfDay() : null;
        $isIndividual = $course->isIndividual();
        $targetChildId = $isIndividual ? (int) $request->input('child_id') : null;

        $courseChildren = $course->children()->withPivot('created_at')->get()->keyBy('id');

        if ($isIndividual) {
            $child = $courseChildren->get($targetChildId);
            if (!$child) {
                return back()->withErrors(['child_id' => 'Wybrany uczestnik nie jest zapisany na te zajęcia.'])->withInput();
            }

            $enrolledAt = ($child->pivot?->created_at ?? $child->created_at)?->startOfDay();
            if ($enrolledAt && $realizedAt && $realizedAt->lt($enrolledAt)) {
                return back()->withErrors([
                    'realized_at' => "Uczeń {$child->name} nie był uczestnikiem zajęć w dniu lekcji ({$realizedAt->format('d.m.Y')}). Data dołączenia do zajęć to {$enrolledAt->format('d.m.Y')}. Data lekcji musi być przynajmniej z dnia dodania ucznia."
                ])->withInput();
            }

            $rawAttendance = $request->input('attendance', []);
            $childAtt = $rawAttendance[$targetChildId] ?? (reset($rawAttendance) ?: []);
            if (empty($childAtt['status'])) {
                $childAtt['status'] = 'present';
            }
            $request->merge(['attendance' => [$targetChildId => $childAtt]]);
        } else {
            // Dla grup: usuwamy z tablicy attendance dzieci, które dołączyły po dacie lekcji,
            // a dla uczestników uprawnionych ustawiamy domyślnie 'present' jeśli status nie dotarł
            $rawAttendance = $request->input('attendance', []);
            if (is_array($rawAttendance)) {
                $cleanedAttendance = [];
                foreach ($rawAttendance as $childId => $attData) {
                    $child = $courseChildren->get((int) $childId);
                    if (!$child) {
                        continue;
                    }
                    $enrolledAt = ($child?->pivot?->created_at ?? $child?->created_at)?->startOfDay();
                    if ($enrolledAt && $realizedAt && $realizedAt->lt($enrolledAt)) {
                        continue;
                    }
                    if (empty($attData['status'])) {
                        $attData['status'] = 'present';
                    }
                    $cleanedAttendance[$childId] = $attData;
                }
                $request->merge(['attendance' => $cleanedAttendance]);
            }
        }

        $validationRules = [
            'realized_at' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,excused'],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($isIndividual) {
            $validationRules['child_id'] = ['required', 'exists:children,id'];
        }

        $customMessages = [
            'realized_at.required' => 'Wybór daty przeprowadzenia zajęć jest wymagany.',
            'realized_at.date' => 'Wprowadź poprawną datę.',
            'child_id.required' => 'Dla zajęć indywidualnych wybór uczestnika jest wymagany.',
            'attendance.required' => 'Lista obecności jest wymagana.',
        ];

        foreach ($courseChildren as $child) {
            $customMessages["attendance.{$child->id}.status.required"] = "Zaznacz status obecności dla uczestnika {$child->name}.";
            $customMessages["attendance.{$child->id}.status.in"] = "Wybierz poprawny status obecności dla uczestnika {$child->name}.";
        }

        $request->validate($validationRules, $customMessages);

        $skippedChildrenNames = [];

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $course, $isIndividual, $targetChildId, $realizedAt, &$skippedChildrenNames) {
            $user = Auth::user();

            $lesson = Lesson::create([
                'course_id' => $course->id,
                'teacher_id' => $user->id,
                'school_id' => $user->school_id,
                'realized_at' => $request->input('realized_at'),
                'topic' => $request->input('topic'),
                'notes' => $request->input('notes'),
            ]);

            $attendances = $request->input('attendance', []);

            if ($isIndividual) {
                // Dla zajęć indywidualnych zapisujemy na liście obecności TYLKO wskazaną osobę
                $data = $attendances[$targetChildId] ?? reset($attendances);
                LessonAttendance::create([
                    'lesson_id' => $lesson->id,
                    'child_id' => $targetChildId,
                    'status' => $data['status'] ?? 'present',
                    'notes' => $data['notes'] ?? null,
                ]);
            } else {
                $children = $course->children()->withPivot('created_at')
                    ->whereIn('children.id', array_keys($attendances))
                    ->get()
                    ->keyBy('id');

                foreach ($attendances as $childId => $data) {
                    $child = $children->get((int) $childId);
                    $enrolledAt = ($child?->pivot?->created_at ?? $child?->created_at)?->startOfDay();

                    // Jeśli lekcja odbyła się przed dołączeniem ucznia do zajęć, pomijamy go w attendances
                    if ($enrolledAt && $realizedAt->lt($enrolledAt)) {
                        if ($child) {
                            $skippedChildrenNames[] = $child->name;
                        }
                        continue;
                    }

                    LessonAttendance::create([
                        'lesson_id' => $lesson->id,
                        'child_id' => (int) $childId,
                        'status' => $data['status'] ?? 'present',
                        'notes' => $data['notes'] ?? null,
                    ]);
                }
            }
        });

        // Automatyczna aktualizacja pozycji rozliczeniowych
        $yearMonth = $realizedAt->format('Y-m');
        $billingService->syncCourseMonth($course, $yearMonth);

        $successMsg = 'Zajęcia zostały pomyślnie zrealizowane, a obecność i pozycje rozliczeniowe zaktualizowane.';
        if (!empty($skippedChildrenNames)) {
            $successMsg .= ' Uwaga: Uczestnicy: ' . implode(', ', $skippedChildrenNames) . ' nie byli jeszcze uczestnikami zajęć w dniu ' . $realizedAt->format('d.m.Y') . ' i nie zostali zapisani na liście obecności.';
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', $successMsg);
    }

    /**
     * Szczegóły konkretnej zrealizowanej lekcji z obecnościami.
     */
    public function show(Lesson $lesson): View
    {
        $user = Auth::user();

        if ($lesson->school_id !== $user->school_id || $lesson->teacher_id !== $user->id) {
            abort(403, 'Brak dostępu do podglądu tej lekcji.');
        }

        $lesson->load(['course', 'attendances.child']);

        return view('teacher.lessons.show', compact('lesson'));
    }

    /**
     * Sprawdza czy nauczyciel ma uprawnienia do tych zajęć.
     */
    private function authorizeTeacherCourse(Course $course): void
    {
        $user = Auth::user();
        if ($course->school_id !== $user->school_id || $course->instructor_id !== $user->id) {
            abort(403, 'Brak uprawnień do prowadzenia tych zajęć.');
        }
    }
}
