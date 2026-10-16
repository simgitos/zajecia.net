<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Domyślny widok rejestracji - utworzenie nowej placówki (Admin).
     */
    public function create(): View
    {
        return view('auth.register', [
            'selectedSchool' => null,
        ]);
    }

    /**
     * Widok rejestracji dedykowany dla konkretnej szkoły (/{slug}/register).
     * Jeśli użytkownik jest już zalogowany, natychmiast przypisuje go do szkoły (pivot) i przełącza widok.
     */
    public function createForSchool(School $school): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->schools()->where('schools.id', $school->id)->exists()) {
                $user->schools()->attach($school->id, [
                    'terms_accepted_at' => null,
                    'roles' => json_encode(['user']),
                ]);
            }

            $schoolRoles = $user->rolesForSchool($school);
            $roleValues = $schoolRoles->map(fn ($r) => $r->value)->toArray();
            if (empty($roleValues)) {
                $roleValues = ['user'];
            }
            $highestRole = $schoolRoles->sortByDesc(fn ($r) => $r->priority())->first();

            session(['active_school_id' => $school->id]);
            session(['active_role' => $highestRole?->value ?? 'user']);
            $user->update([
                'school_id' => $school->id,
                'roles' => $roleValues,
            ]);

            return redirect()->route('dashboard')->with(
                'success',
                "Placówka \"{$school->name}\" została przypisana do Twojego konta. Prosimy o zaakceptowanie regulaminu na pulpicie."
            );
        }

        return view('auth.register', [
            'selectedSchool' => $school,
        ]);
    }

    /**
     * Obsługa rejestracji użytkownika.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isSchoolRegistration = $request->filled('school_id');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'school_id' => [$isSchoolRegistration ? 'required' : 'nullable', 'exists:schools,id'],
            'new_school_name' => [!$isSchoolRegistration ? 'required' : 'nullable', 'string', 'max:255'],
            'terms_and_conditions' => ['required', 'accepted'],
        ]);

        if ($isSchoolRegistration) {
            // Rejestracja zwykłego użytkownika z dedykowanego adresu szkoły
            $schoolId = (int) $request->input('school_id');
            $userRoles = [UserRole::USER];
            $termsAcceptedAt = null; // Wymóg akceptacji regulaminu placówki w dashboardzie
        } else {
            // Standardowa rejestracja bez sluga -> Utworzenie nowej szkoły (Admin)
            $schoolName = trim($request->input('new_school_name'));
            $baseSlug = Str::slug($schoolName);
            $slug = $baseSlug ?: 'szkola-' . Str::random(5);
            $count = 1;
            while (School::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $school = School::create([
                'name' => $schoolName,
                'slug' => $slug,
                'email' => $request->email,
            ]);

            $schoolId = $school->id;
            $userRoles = [UserRole::ADMIN];
            $termsAcceptedAt = now();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $schoolId,
            'roles' => $userRoles,
        ]);

        // Dodanie wpisu do pivota school_user
        $rolesArray = array_map(fn ($r) => $r instanceof UserRole ? $r->value : (string) $r, $userRoles);
        $user->schools()->attach($schoolId, [
            'terms_accepted_at' => $termsAcceptedAt,
            'roles' => json_encode($rolesArray),
        ]);

        session(['active_school_id' => $schoolId]);
        session(['active_role' => $userRoles[0]->value]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
