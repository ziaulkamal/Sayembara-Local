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
        // Kalau mau global middleware (setiap request) bisa pakai:
        // $middleware->append(\App\Http\Middleware\SomeGlobalMiddleware::class);

        // Ini untuk route middleware, dipakai di route dengan ->middleware('guestlogin')
        $middleware->alias([
            'guest.login' => \App\Http\Middleware\GuestLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
