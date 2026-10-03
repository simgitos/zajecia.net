<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Wyświetla listę użytkowników.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Formularz edycji użytkownika.
     */
    public function edit(User $user)
    {
        $roles = UserRole::cases();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Aktualizacja danych użytkownika.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'roles' => [$request->role],
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwanie użytkownika.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'Użytkownik został usunięty.');
    }
}
