<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Lista zajęć przypisanych do zalogowanego nauczyciela.
     */
    public function index(): View
    {
        $user = Auth::user();

        $courses = Course::where('school_id', $user->school_id)
            ->where('instructor_id', $user->id)
            ->withCount('children')
            ->with(['room'])
            ->latest()
            ->get();

        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Szczegóły konkretnych zajęć nauczyciela z listą zapisanych dzieci.
     */
    public function show(Course $course): View
    {
        $user = Auth::user();

        if ($course->school_id !== $user->school_id || $course->instructor_id !== $user->id) {
            abort(403, 'Dostęp zabroniony. Nie jesteś przypisanym nauczycielem tych zajęć.');
        }

        $course->load(['room', 'children.parent'])
            ->loadCount('children');

        return view('teacher.courses.show', compact('course'));
    }
}
