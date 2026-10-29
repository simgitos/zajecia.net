<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SwitchSeasonController extends Controller
{
    /**
     * Przełącza aktywny sezon użytkownika w sesji dla aktualnej placówki.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'season_id' => ['required', 'integer', 'exists:seasons,id'],
        ]);

        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $school = $user->activeSchool();

        if (!$school) {
            abort(404, 'Brak aktywnej placówki.');
        }

        $season = $school->seasons()->where('id', (int) $validated['season_id'])->first();

        if (!$season) {
            abort(403, 'Wybrany sezon nie należy do Twojej placówki.');
        }

        session(['active_season_id' => $season->id]);

        return back()->with('success', 'Przełączono aktywny sezon na: ' . $season->name);
    }
}
