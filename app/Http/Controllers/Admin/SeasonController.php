<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSeasonRequest;
use App\Http\Requests\Admin\UpdateSeasonRequest;
use App\Models\Course;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SeasonController extends Controller
{
    /**
     * Lista wszystkich sezonów (semestrów/lat szkolnych) placówki.
     */
    public function index(): View
    {
        $schoolId = Auth::user()->school_id;

        $seasons = Season::where('school_id', $schoolId)
            ->withCount('courses')
            ->orderByDesc('start_date')
            ->paginate(15);

        // Liczba unikalnych zapisanych uczestników per sezon
        foreach ($seasons as $season) {
            $season->enrolled_children_count = DB::table('child_course')
                ->join('courses', 'child_course.course_id', '=', 'courses.id')
                ->where('courses.season_id', $season->id)
                ->distinct('child_course.child_id')
                ->count('child_course.child_id');
        }

        $activeSeason = Season::where('school_id', $schoolId)->where('is_active', true)->first();

        return view('admin.seasons.index', compact('seasons', 'activeSeason'));
    }

    /**
     * Formularz dodawania nowego sezonu.
     */
    public function create(): View
    {
        $schoolId = Auth::user()->school_id;
        $existingSeasons = Season::where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();

        return view('admin.seasons.create', compact('existingSeasons'));
    }

    /**
     * Zapis nowego sezonu i opcjonalne skopiowanie oferty zajęć bez uczestników.
     */
    public function store(StoreSeasonRequest $request): RedirectResponse
    {
        $schoolId = Auth::user()->school_id;
        $isActive = $request->boolean('is_active');

        // Jeśli to pierwszy sezon w szkole, automatycznie go aktywuj
        if (!Season::where('school_id', $schoolId)->exists()) {
            $isActive = true;
        }

        DB::transaction(function () use ($request, $schoolId, $isActive, &$season, &$copiedCount) {
            if ($isActive) {
                Season::where('school_id', $schoolId)->update(['is_active' => false]);
            }

            $season = Season::create([
                'school_id' => $schoolId,
                'name' => $request->validated('name'),
                'start_date' => $request->validated('start_date'),
                'end_date' => $request->validated('end_date'),
                'is_active' => $isActive,
            ]);

            $copiedCount = 0;
            $copyFromSeasonId = $request->input('copy_courses_from_season_id');

            if ($copyFromSeasonId) {
                $sourceCourses = Course::where('school_id', $schoolId)
                    ->where('season_id', $copyFromSeasonId)
                    ->get();

                foreach ($sourceCourses as $src) {
                    Course::create([
                        'school_id' => $schoolId,
                        'season_id' => $season->id,
                        'instructor_id' => $src->instructor_id,
                        'room_id' => $src->room_id,
                        'title' => $src->title,
                        'type' => $src->type,
                        'billing_type' => $src->billing_type,
                        'price_per_unit' => $src->price_per_unit,
                        'max_participants' => $src->max_participants,
                        'min_age' => $src->min_age,
                        'max_age' => $src->max_age,
                        'description' => $src->description,
                        'schedule_info' => $src->schedule_info,
                        'is_active' => true,
                    ]);
                    $copiedCount++;
                }
            }
        });

        $message = "Sezon \"{$season->name}\" został pomyślnie utworzony.";
        if (isset($copiedCount) && $copiedCount > 0) {
            $message .= " Skopiowano {$copiedCount} zajęć jako gotowe szablony (uczestnicy muszą zapisać się ponownie).";
        }

        return redirect()->route('admin.seasons.index')->with('success', $message);
    }

    /**
     * Formularz edycji sezonu.
     */
    public function edit(Season $season): View
    {
        if ($season->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        return view('admin.seasons.edit', compact('season'));
    }

    /**
     * Aktualizacja sezonu.
     */
    public function update(UpdateSeasonRequest $request, Season $season): RedirectResponse
    {
        if ($season->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $isActive = $request->boolean('is_active');

        DB::transaction(function () use ($request, $season, $isActive) {
            if ($isActive) {
                Season::where('school_id', $season->school_id)
                    ->where('id', '!=', $season->id)
                    ->update(['is_active' => false]);
            }

            $season->update([
                'name' => $request->validated('name'),
                'start_date' => $request->validated('start_date'),
                'end_date' => $request->validated('end_date'),
                'is_active' => $isActive,
            ]);
        });

        return redirect()->route('admin.seasons.index')
            ->with('success', "Sezon \"{$season->name}\" został zaktualizowany.");
    }

    /**
     * Aktywacja sezonu jako bieżącego dla placówki.
     */
    public function activate(Season $season): RedirectResponse
    {
        if ($season->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        DB::transaction(function () use ($season) {
            Season::where('school_id', $season->school_id)->update(['is_active' => false]);
            $season->update(['is_active' => true]);
        });

        return redirect()->route('admin.seasons.index')
            ->with('success', "Sezon \"{$season->name}\" został ustawiony jako bieżący aktywny sezon placówki.");
    }

    /**
     * Usunięcie sezonu.
     */
    public function destroy(Season $season): RedirectResponse
    {
        if ($season->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        if ($season->is_active) {
            return redirect()->route('admin.seasons.index')
                ->with('error', 'Nie możesz usunąć aktywnego sezonu. Najpierw aktywuj inny sezon.');
        }

        $remainingCount = Season::where('school_id', $season->school_id)->count();
        if ($remainingCount <= 1) {
            return redirect()->route('admin.seasons.index')
                ->with('error', 'Placówka musi posiadać przynajmniej jeden sezon.');
        }

        $season->delete();

        return redirect()->route('admin.seasons.index')
            ->with('success', 'Sezon został usunięty.');
    }
}
