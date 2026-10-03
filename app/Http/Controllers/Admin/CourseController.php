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
        if ($course->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $course->load(['instructor', 'room', 'children.parent'])
            ->loadCount('children');

        return view('admin.courses.show', compact('course'));
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

        return view('admin.courses.create', compact('rooms', 'instructors'));
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

        Course::create([
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
}
