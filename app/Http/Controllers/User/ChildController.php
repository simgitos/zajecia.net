<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EnrollChildRequest;
use App\Http\Requests\User\StoreChildRequest;
use App\Http\Requests\User\UpdateChildRequest;
use App\Models\Child;
use App\Models\Course;
use App\Services\CourseEnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * Lista dzieci rodzica wraz z zapisanymi zajęciami i dostępnymi kursami.
     */
    public function index(): View
    {
        $user = auth()->user();
        $school = $user->activeSchool() ?? $user->school;
        $activeSeason = $school?->activeSeason();

        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->with(['courses' => function ($q) use ($activeSeason) {
                if ($activeSeason) {
                    $q->where('courses.season_id', $activeSeason->id);
                }
                $q->with(['instructor', 'room', 'season']);
            }])
            ->latest()
            ->get();

        $coursesQuery = Course::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->withCount('children')
            ->with(['instructor', 'room', 'season'])
            ->orderBy('title');

        if ($activeSeason) {
            $coursesQuery->where('season_id', $activeSeason->id);
        }

        $courses = $coursesQuery->get();

        return view('user.children.index', compact('children', 'courses', 'activeSeason'));
    }

    /**
     * Dodanie nowego dziecka.
     */
    public function store(StoreChildRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validated();
        $data['parent_id'] = $user->id;
        $data['school_id'] = $user->school_id;

        Child::create($data);

        return redirect()->route('user.children.index')
            ->with('success', 'Dziecko zostało pomyślnie dodane.');
    }

    /**
     * Formularz edycji dziecka.
     */
    public function edit(Child $child): View
    {
        $this->authorizeChildOwnership($child);

        return view('user.children.edit', compact('child'));
    }

    /**
     * Aktualizacja danych dziecka.
     */
    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $this->authorizeChildOwnership($child);

        $child->update($request->validated());

        return redirect()->route('user.children.index')
            ->with('success', 'Dane dziecka zostały pomyślnie zaktualizowane.');
    }

    /**
     * Usuwanie dziecka z systemu.
     */
    public function destroy(Child $child): RedirectResponse
    {
        $this->authorizeChildOwnership($child);

        $child->delete();

        return redirect()->route('user.children.index')
            ->with('success', 'Profil dziecka został usunięty.');
    }

    /**
     * Przypisanie dziecka do wybranego kursu (zajęć).
     */
    public function enroll(EnrollChildRequest $request, CourseEnrollmentService $service): RedirectResponse
    {
        $user = auth()->user();
        $child = Child::where('id', $request->input('child_id'))
            ->where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $course = Course::where('id', $request->input('course_id'))
            ->where('school_id', $user->school_id)
            ->firstOrFail();

        $service->enroll($child, $course, $user);

        return redirect()->back()
            ->with('success', "Dziecko {$child->name} zostało pomyślnie zapisane na zajęcia \"{$course->title}\".");
    }

    /**
     * Wypisanie dziecka z zajęć.
     */
    public function unenroll(Child $child, Course $course, CourseEnrollmentService $service): RedirectResponse
    {
        $user = auth()->user();

        $service->unenroll($child, $course, $user);

        return redirect()->back()
            ->with('success', "Dziecko {$child->name} zostało wypisane z zajęć \"{$course->title}\".");
    }

    /**
     * Katalog wszystkich aktywnych zajęć w szkole dla użytkownika.
     */
    public function coursesCatalog(): View
    {
        $user = auth()->user();
        $school = $user->activeSchool() ?? $user->school;
        $activeSeason = $school?->activeSeason();

        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->get();

        $coursesQuery = Course::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->withCount('children')
            ->with(['instructor', 'room', 'children', 'season'])
            ->orderBy('title');

        if ($activeSeason) {
            $coursesQuery->where('season_id', $activeSeason->id);
        }

        $courses = $coursesQuery->get();

        return view('user.courses.index', compact('courses', 'children', 'activeSeason'));
    }

    /**
     * Sprawdza czy zalogowany użytkownik jest rodzicem danego dziecka.
     */
    private function authorizeChildOwnership(Child $child): void
    {
        if ($child->parent_id !== auth()->id() || $child->school_id !== auth()->user()->school_id) {
            abort(403, 'Brak uprawnień do modyfikacji profilu tego dziecka.');
        }
    }
}
