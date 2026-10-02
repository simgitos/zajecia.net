<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Zamiana ciągów tekstowych z tras (np. 'admin') na obiekty Enum
        $enumRoles = array_filter(
            array_map(fn (string $role) => UserRole::tryFrom($role), $roles)
        );

        if (!$user->hasRole($enumRoles)) {
            abort(403, 'Brak wymaganych uprawnień.');
        }

        return $next($request);
    }
}