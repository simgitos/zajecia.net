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
            abort(403, 'Dostęp zabroniony. Nie jesteś przypisanym prowadzącym tych zajęć.');
        }

        $course->load(['room', 'children.parent', 'lessons.attendances', 'lessons.attendances.child'])
            ->loadCount(['children', 'lessons']);

        $availableChildren = \App\Models\Child::where('school_id', $user->school_id)
            ->whereNotIn('id', $course->children->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('teacher.courses.show', compact('course', 'availableChildren'));
    }

    /**
     * Zmiana kolejności uczestników na zajęciach (Drag & Drop).
     */
    public function reorderChildren(\Illuminate\Http\Request $request, Course $course): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if ($course->school_id !== $user->school_id || $course->instructor_id !== $user->id) {
            abort(403, 'Dostęp zabroniony.');
        }

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:children,id'],
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $childId) {
            \Illuminate\Support\Facades\DB::table('child_course')
                ->where('course_id', $course->id)
                ->where('child_id', $childId)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kolejność uczestników została pomyślnie zapisana.',
        ]);
    }

    /**
     * Przypisanie wielu uczestników jednocześnie do zajęć.
     */
    public function enrollChildren(\Illuminate\Http\Request $request, Course $course): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($course->school_id !== $user->school_id || $course->instructor_id !== $user->id) {
            abort(403, 'Dostęp zabroniony.');
        }

        $request->validate([
            'child_ids' => ['required', 'array', 'min:1'],
            'child_ids.*' => ['integer', 'exists:children,id'],
        ], [
            'child_ids.required' => 'Wybierz przynajmniej jednego uczestnika do dodania.',
        ]);

        $childIds = $request->input('child_ids');
        $currentMaxOrder = (int) \Illuminate\Support\Facades\DB::table('child_course')
            ->where('course_id', $course->id)
            ->max('sort_order');

        $addedCount = 0;
        foreach ($childIds as $childId) {
            // Sprawdzenie czy dziecko należy do szkoły
            $child = \App\Models\Child::where('school_id', $user->school_id)->find($childId);
            if (!$child) {
                continue;
            }

            if (!$course->children()->where('children.id', $childId)->exists()) {
                $currentMaxOrder++;
                $course->children()->attach($childId, [
                    'sort_order' => $currentMaxOrder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $addedCount++;
            }
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', "Pomyślnie dodano {$addedCount} uczestników do zajęć.");
    }

    /**
     * Usunięcie uczestnika z zajęć.
     */
    public function unenrollChild(Course $course, \App\Models\Child $child): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($course->school_id !== $user->school_id || $course->instructor_id !== $user->id) {
            abort(403, 'Dostęp zabroniony.');
        }

        $course->children()->detach($child->id);

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', "Uczestnik {$child->name} został wypisany z zajęć.");
    }
}
