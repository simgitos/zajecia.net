<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    /**
     * Weryfikuje, czy zalogowany użytkownik ma uprawnienia SuperAdmina (SU).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!$user->isSuperAdmin()) {
            abort(403, 'Brak uprawnień SuperAdmina (SU). Dostęp zabroniony.');
        }

        return $next($request);
    }
}
