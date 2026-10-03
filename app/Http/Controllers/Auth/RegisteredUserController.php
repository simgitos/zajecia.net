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
     * Widok rejestracji dedykowany dla konkretnej szkoły (/{slug}/register) - Zwykły użytkownik.
     */
    public function createForSchool(School $school): View
    {
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
            $schoolId = $request->input('school_id');
            $userRoles = [UserRole::USER];
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
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $schoolId,
            'roles' => $userRoles,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
