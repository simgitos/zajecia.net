<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Super-Admin Bypass
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole(UserRole::ADMIN)) {
                return true;
            }
        });

        Paginator::useBootstrapFive();

        // 2. Dyrektywa Blade @role(...) / @endrole
        Blade::if('role', function (UserRole|string|array $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // 3. Dyrektywa Blade @activeRole(...) / @endactiveRole
        Blade::if('activeRole', function (UserRole|string|array $role) {
            if (!auth()->check()) {
                return false;
            }
            $activeRole = auth()->user()->activeRole();
            if (!$activeRole) {
                return false;
            }
            $rolesArray = is_array($role) ? $role : [$role];
            foreach ($rolesArray as $r) {
                $val = $r instanceof UserRole ? $r->value : (string) $r;
                if ($activeRole->value === $val) {
                    return true;
                }
            }
            return false;
        });
    }
}