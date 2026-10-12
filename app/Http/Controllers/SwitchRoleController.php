<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SwitchRoleController extends Controller
{
    /**
     * Przełącza aktywną rolę użytkownika w sesji.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::enum(UserRole::class)],
        ]);

        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $targetRole = UserRole::from($validated['role']);

        if (!$user->hasRole($targetRole)) {
            abort(403, 'Nie posiadasz uprawnień do wybranej roli.');
        }

        session(['active_role' => $targetRole->value]);

        return redirect()->route('dashboard')->with('success', 'Przełączono aktywną rolę na: ' . $targetRole->label());
    }
}
