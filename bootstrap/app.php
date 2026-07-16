<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust semua proxy headers dari Nginx reverse proxy
        // Diperlukan karena app berjalan di belakang Nginx (VM publik)
        // yang meneruskan request ke k3s VM internal (10.10.0.x)
        // Tanpa ini: Laravel generate URL dengan http:// padahal seharusnya https://
        // dan IP client akan terbaca sebagai IP Nginx bukan IP user sebenarnya
        $middleware->trustProxies(
            at: '*',   // Trust semua proxy — aman karena app hanya bisa diakses dari dalam VPC
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PREFIX,
        );

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
