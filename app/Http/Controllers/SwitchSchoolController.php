<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SwitchSchoolController extends Controller
{
    /**
     * Przełącza aktywną szkołę użytkownika w sesji oraz synchronizuje pole school_id w bazie danych.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
        ]);

        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $schoolId = (int) $validated['school_id'];

        $membership = $user->schools()->where('schools.id', $schoolId)->first();

        if (!$membership) {
            abort(403, 'Nie należysz do wybranej placówki.');
        }

        $school = School::findOrFail($schoolId);

        session(['active_school_id' => $school->id]);
        session()->forget('active_season_id');

        // Pobierz role użytkownika dla nowej placówki
        $schoolRoles = $user->rolesForSchool($school);
        $roleValues = $schoolRoles->map(fn ($r) => $r->value)->toArray();
        if (empty($roleValues)) {
            $roleValues = ['user'];
        }

        // Ustaw aktywną rolę na najwyższą z uprawnień w nowej szkole
        $highestRole = $schoolRoles->sortByDesc(fn ($r) => $r->priority())->first();
        session(['active_role' => $highestRole?->value ?? 'user']);

        return redirect()->route('dashboard')->with('success', 'Przełączono aktywną placówkę na: ' . $school->name);
    }
}
