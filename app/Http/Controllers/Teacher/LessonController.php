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
     * Formularz przeprowadzania / realizacji nowej lekcji.
     */
    public function create(Request $request, Course $course): View
    {
        $this->authorizeTeacherCourse($course);

        $course->load('children');

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

        $validationRules = [
            'realized_at' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,excused'],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($course->isIndividual()) {
            $validationRules['child_id'] = ['required', 'exists:children,id'];
        }

        $request->validate($validationRules, [
            'realized_at.required' => 'Wybór daty przeprowadzenia zajęć jest wymagany.',
            'realized_at.date' => 'Wprowadź poprawną datę.',
            'child_id.required' => 'Dla zajęć indywidualnych wybór uczestnika jest wymagany.',
            'attendance.required' => 'Lista obecności jest wymagana.',
        ]);

        $isIndividual = $course->isIndividual();
        $targetChildId = $isIndividual ? (int) $request->input('child_id') : null;

        if ($isIndividual && !$course->children()->where('children.id', $targetChildId)->exists()) {
            return back()->withErrors(['child_id' => 'Wybrany uczestnik nie jest zapisany na te zajęcia.'])->withInput();
        }

        DB::transaction(function () use ($request, $course, $isIndividual, $targetChildId) {
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
                // Dla zajęć indywidualnych zapisujemy na liście obecności TYLKO JEDNĄ OSOBĘ
                $data = $attendances[$targetChildId] ?? reset($attendances);
                LessonAttendance::create([
                    'lesson_id' => $lesson->id,
                    'child_id' => $targetChildId,
                    'status' => $data['status'] ?? 'present',
                    'notes' => $data['notes'] ?? null,
                ]);
            } else {
                foreach ($attendances as $childId => $data) {
                    LessonAttendance::create([
                        'lesson_id' => $lesson->id,
                        'child_id' => $childId,
                        'status' => $data['status'] ?? 'present',
                        'notes' => $data['notes'] ?? null,
                    ]);
                }
            }
        });

        // Automatyczna aktualizacja pozycji rozliczeniowych
        $yearMonth = \Carbon\Carbon::parse($request->input('realized_at'))->format('Y-m');
        $billingService->syncCourseMonth($course, $yearMonth);

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Zajęcia zostały pomyślnie zrealizowane, a obecność i pozycje rozliczeniowe zaktualizowane.');
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
