<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Wyświetla listę zajęć/kursów dla szkoły administratora.
     */
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $courses = Course::with(['instructor', 'room'])
            ->withCount('children')
            ->where('school_id', $schoolId)
            ->latest()
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Szczegóły konkretnych zajęć z listą zapisanych dzieci.
     */
    public function show(Course $course)
    {
        $schoolId = Auth::user()->school_id;
        if ($course->school_id !== $schoolId) {
            abort(403);
        }

        $course->load(['instructor', 'room', 'children.parent', 'lessons.attendances', 'lessons.attendances.child', 'lessons.teacher'])
            ->loadCount(['children', 'lessons']);

        $availableChildren = \App\Models\Child::where('school_id', $schoolId)
            ->whereNotIn('id', $course->children->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('admin.courses.show', compact('course', 'availableChildren'));
    }

    /**
     * Formularz dodawania nowych zajęć.
     */
    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $rooms = Room::where('school_id', $schoolId)->orderBy('name')->get();
        $instructors = User::where('school_id', $schoolId)
            ->whereJsonContains('roles', UserRole::TEACHER->value)
            ->orderBy('name')->get();
        $children = \App\Models\Child::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        return view('admin.courses.create', compact('rooms', 'instructors', 'children'));
    }

    /**
     * Zapis nowych zajęć w bazie.
     */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        if ($request->input('room_id') === 'NEW') {
            $request->merge(['room_id' => null]);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructor_id' => ['required', 'exists:users,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'new_room_name' => ['required_if:room_id,NEW', 'nullable', 'string', 'max:255'],
            'type' => ['required', 'in:group,individual'],
            'billing_type' => ['required', 'in:monthly_flat,per_lesson_monthly,per_lesson_single'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'max_participants' => ['required', 'integer', 'min:1'],
            'min_age' => ['nullable', 'integer', 'min:0'],
            'max_age' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'child_ids' => ['nullable', 'array'],
            'child_ids.*' => ['integer', 'exists:children,id'],
            'create_separate_for_each' => ['nullable', 'boolean'],
        ]);

        $roomId = $request->input('room_id');

        // Jeśli wpisano nową salę, utwórz ją w bazie dla tej szkoły
        if ($request->filled('new_room_name')) {
            $room = Room::create([
                'school_id' => $schoolId,
                'name' => trim($request->input('new_room_name')),
            ]);
            $roomId = $room->id;
        }

        $selectedChildIds = $request->input('child_ids', []);
        $separateForEach = $request->boolean('create_separate_for_each') && $request->type === 'individual' && !empty($selectedChildIds);

        if ($separateForEach) {
            // Tworzenie osobnych zajęć indywidualnych dla każdego zaznaczonego uczestnika jednocześnie
            foreach ($selectedChildIds as $childId) {
                $child = \App\Models\Child::where('school_id', $schoolId)->find($childId);
                $title = $child ? $request->title . ' - ' . $child->name : $request->title;

                $newCourse = Course::create([
                    'school_id' => $schoolId,
                    'instructor_id' => $request->instructor_id,
                    'room_id' => $roomId,
                    'title' => $title,
                    'type' => 'individual',
                    'billing_type' => $request->billing_type,
                    'price_per_unit' => $request->price_per_unit,
                    'max_participants' => 1,
                    'min_age' => $request->min_age,
                    'max_age' => $request->max_age,
                    'description' => $request->description,
                    'is_active' => $request->has('is_active'),
                ]);

                $newCourse->children()->attach($childId, [
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('admin.courses.index')
                ->with('success', 'Pomyślnie utworzono osobne zajęcia indywidualne dla ' . count($selectedChildIds) . ' uczestników.');
        }

        $course = Course::create([
            'school_id' => $schoolId,
            'instructor_id' => $request->instructor_id,
            'room_id' => $roomId,
            'title' => $request->title,
            'type' => $request->type,
            'billing_type' => $request->billing_type,
            'price_per_unit' => $request->price_per_unit,
            'max_participants' => $request->max_participants,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($selectedChildIds)) {
            $order = 1;
            foreach ($selectedChildIds as $childId) {
                $course->children()->attach($childId, [
                    'sort_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.courses.index')->with('success', 'Zajęcia zostały pomyślnie dodane.');
    }

    /**
     * Formularz edycji zajęć.
     */
    public function edit(Course $course)
    {
        $schoolId = Auth::user()->school_id;
        if ($course->school_id !== $schoolId) {
            abort(403);
        }

        $rooms = Room::where('school_id', $schoolId)->orderBy('name')->get();
        $instructors = User::where('school_id', $schoolId)
            ->whereJsonContains('roles', UserRole::TEACHER->value)
            ->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'rooms', 'instructors'));
    }

    /**
     * Aktualizacja zajęć w bazie.
     */
    public function update(Request $request, Course $course)
    {
        $schoolId = Auth::user()->school_id;
        if ($course->school_id !== $schoolId) {
            abort(403);
        }

        if ($request->input('room_id') === 'NEW') {
            $request->merge(['room_id' => null]);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructor_id' => ['required', 'exists:users,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'new_room_name' => ['required_if:room_id,NEW', 'nullable', 'string', 'max:255'],
            'type' => ['required', 'in:group,individual'],
            'billing_type' => ['required', 'in:monthly_flat,per_lesson_monthly,per_lesson_single'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'max_participants' => ['required', 'integer', 'min:1'],
            'min_age' => ['nullable', 'integer', 'min:0'],
            'max_age' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $roomId = $request->input('room_id');

        if ($request->filled('new_room_name')) {
            $room = Room::create([
                'school_id' => $schoolId,
                'name' => trim($request->input('new_room_name')),
            ]);
            $roomId = $room->id;
        }

        $course->update([
            'instructor_id' => $request->instructor_id,
            'room_id' => $roomId,
            'title' => $request->title,
            'type' => $request->type,
            'billing_type' => $request->billing_type,
            'price_per_unit' => $request->price_per_unit,
            'max_participants' => $request->max_participants,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Zajęcia zostały pomyślnie zaktualizowane.');
    }

    /**
     * Usuwanie zajęć.
     */
    public function destroy(Course $course)
    {
        if ($course->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Zajęcia zostały usunięte.');
    }

    /**
     * Zmiana kolejności uczestników na zajęciach (Drag & Drop).
     */
    public function reorderChildren(Request $request, Course $course): \Illuminate\Http\JsonResponse
    {
        if ($course->school_id !== Auth::user()->school_id) {
            abort(403);
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
    public function enrollChildren(Request $request, Course $course): \Illuminate\Http\RedirectResponse
    {
        if ($course->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $request->validate([
            'child_ids' => ['required', 'array', 'min:1'],
            'child_ids.*' => ['integer', 'exists:children,id'],
        ], [
            'child_ids.required' => 'Wybierz przynajmniej jednego uczestnika do dodania.',
        ]);

        $childIds = $request->input('child_ids');
        $schoolId = Auth::user()->school_id;
        $currentMaxOrder = (int) \Illuminate\Support\Facades\DB::table('child_course')
            ->where('course_id', $course->id)
            ->max('sort_order');

        $addedCount = 0;
        foreach ($childIds as $childId) {
            $child = \App\Models\Child::where('school_id', $schoolId)->find($childId);
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

        return redirect()->route('admin.courses.show', $course)
            ->with('success', "Pomyślnie dodano {$addedCount} uczestników do zajęć.");
    }

    /**
     * Usunięcie uczestnika z zajęć przez administratora.
     */
    public function unenrollChild(Course $course, \App\Models\Child $child): \Illuminate\Http\RedirectResponse
    {
        if ($course->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $course->children()->detach($child->id);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', "Uczestnik {$child->name} został wypisany z zajęć.");
    }
}
