<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'su' => \App\Http\Middleware\EnsureIsSuperAdmin::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'tpay/webhook',
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();