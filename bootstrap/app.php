<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'waiter' => \App\Http\Middleware\EnsureWaiter::class,
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
            'session.timeout' => \App\Http\Middleware\HandleSessionTimeout::class,
            'session.activity' => \App\Http\Middleware\SessionActivityTracker::class,
            'multi.auth' => \App\Http\Middleware\MultiSessionAuth::class,
        ]);

        // Global middleware
        $middleware->web(append: [
            \App\Http\Middleware\SessionActivityTracker::class,
            \App\Http\Middleware\HandleSessionTimeout::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
