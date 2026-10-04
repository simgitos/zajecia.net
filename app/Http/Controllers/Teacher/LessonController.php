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
    public function create(Course $course): View
    {
        $this->authorizeTeacherCourse($course);

        $course->load('children');

        return view('teacher.lessons.create', compact('course'));
    }

    /**
     * Zapis realizacji lekcji wraz ze sprawdzoną listą obecności.
     */
    public function store(Request $request, Course $course, \App\Services\BillingService $billingService): RedirectResponse
    {
        $this->authorizeTeacherCourse($course);

        $request->validate([
            'realized_at' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,excused'],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ], [
            'realized_at.required' => 'Wybór daty przeprowadzenia lekcji jest wymagany.',
            'realized_at.date' => 'Wprowadź poprawną datę.',
            'attendance.required' => 'Lista obecności dzieci jest wymagana.',
        ]);

        DB::transaction(function () use ($request, $course) {
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
            foreach ($attendances as $childId => $data) {
                LessonAttendance::create([
                    'lesson_id' => $lesson->id,
                    'child_id' => $childId,
                    'status' => $data['status'] ?? 'present',
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        });

        // Automatyczna aktualizacja pozycji rozliczeniowych w nowej tabeli billing_items dla tego miesiąca
        $yearMonth = \Carbon\Carbon::parse($request->input('realized_at'))->format('Y-m');
        $billingService->syncCourseMonth($course, $yearMonth);

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Lekcja została pomyślnie zrealizowana, a pozycje rozliczeniowe i lista obecności zaktualizowane.');
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
