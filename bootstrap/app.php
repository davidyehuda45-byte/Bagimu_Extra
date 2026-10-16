<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckSessionVersion::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Percayai reverse proxy / load balancer bila dikonfigurasi
        // (wajib saat di belakang Nginx proxy / Cloudflare agar
        // deteksi HTTPS dan IP pengunjung benar). Isi TRUSTED_PROXIES
        // di .env, mis. "*" atau "10.0.0.0/8".
        $proxies = env('TRUSTED_PROXIES');
        if (is_string($proxies) && trim($proxies) !== '') {
            $at = trim($proxies) === '*'
                ? '*'
                : array_map('trim', explode(',', $proxies));
            $middleware->trustProxies(at: $at);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
