<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Wyświetla listę użytkowników szkoły administratora.
     */
    public function index(): View
    {
        $schoolId = Auth::user()->school_id;
        $users = User::where(function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId)
                ->orWhereHas('schools', fn ($q) => $q->where('schools.id', $schoolId));
        })->distinct()->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Formularz edycji użytkownika.
     */
    public function edit(User $user): View
    {
        $adminSchoolId = Auth::user()->school_id;
        $isMember = $user->school_id === $adminSchoolId || $user->schools()->where('schools.id', $adminSchoolId)->exists();
        if (!$isMember) {
            abort(403);
        }

        $roles = UserRole::cases();
        $userSchoolRoles = $user->rolesForSchool($adminSchoolId);
        return view('admin.users.edit', compact('user', 'roles', 'userSchoolRoles'));
    }

    /**
     * Aktualizacja danych użytkownika.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $adminSchoolId = Auth::user()->school_id;
        $isMember = $user->school_id === $adminSchoolId || $user->schools()->where('schools.id', $adminSchoolId)->exists();
        if (!$isMember) {
            abort(403);
        }

        $newRoles = $request->validated('roles');

        // Zaktualizuj lub dodaj wpis w tabeli pivot school_user dla tej szkoły
        if ($user->schools()->where('schools.id', $adminSchoolId)->exists()) {
            $user->schools()->updateExistingPivot($adminSchoolId, [
                'roles' => json_encode($newRoles),
            ]);
        } else {
            $user->schools()->attach($adminSchoolId, [
                'terms_accepted_at' => now(),
                'roles' => json_encode($newRoles),
            ]);
        }

        // Jeśli edytowany użytkownik ma tę szkołę jako aktywną, zaktualizuj też jego cache roles
        if ($user->school_id === $adminSchoolId) {
            $user->update([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'roles' => $newRoles,
            ]);
        } else {
            $user->update([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
            ]);
        }

        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwanie użytkownika z placówki.
     */
    public function destroy(User $user)
    {
        $adminSchoolId = Auth::user()->school_id;
        $isMember = $user->school_id === $adminSchoolId || $user->schools()->where('schools.id', $adminSchoolId)->exists();
        if (!$isMember) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.user.index')->with('error', 'Nie możesz usunąć własnego konta administratora.');
        }

        // Odłącz użytkownika z tej placówki
        $user->schools()->detach($adminSchoolId);

        // Jeśli to była jedyna placówka użytkownika, usuń konto
        if ($user->schools()->count() === 0) {
            $user->delete();
        } elseif ($user->school_id === $adminSchoolId) {
            // Przepnij na inną dostępną placówkę
            $nextSchool = $user->schools()->first();
            $nextRoles = $nextSchool ? $user->rolesForSchool($nextSchool->id)->map(fn ($r) => $r->value)->toArray() : ['user'];
            $user->update([
                'school_id' => $nextSchool?->id,
                'roles' => $nextRoles,
            ]);
        }

        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został odłączony od placówki.');
    }
}
