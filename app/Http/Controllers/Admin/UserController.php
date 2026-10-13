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
        $users = User::where('school_id', $schoolId)->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Formularz edycji użytkownika.
     */
    public function edit(User $user): View
    {
        if ($user->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $roles = UserRole::cases();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Aktualizacja danych użytkownika.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $user->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'roles' => $request->validated('roles'),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwanie użytkownika.
     */
    public function destroy(User $user)
    {
        if ($user->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.user.index')->with('error', 'Nie możesz usunąć własnego konta administratora.');
        }

        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został usunięty.');
    }
}
