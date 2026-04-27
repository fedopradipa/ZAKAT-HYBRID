<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Mendaftarkan alias 'role' agar bisa digunakan di web.php
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Mengatur tujuan redirect otomatis jika user belum login
        $middleware->redirectGuestsTo(fn() => route('login'));

        // Pengecualian CSRF untuk rute Webhook
        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();