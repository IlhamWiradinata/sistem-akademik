<?php

// ===== TAMBAHKAN INI DI AWAL FILE =====
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    // Redirect storage ke /tmp
    putenv("STORAGE_PATH=/tmp/storage");
    putenv("CACHE_PATH=/tmp/cache");

    // Buat direktori jika belum ada
    if (!is_dir('/tmp/storage')) mkdir('/tmp/storage', 0755, true);
    if (!is_dir('/tmp/storage/logs')) mkdir('/tmp/storage/logs', 0755, true);
    if (!is_dir('/tmp/cache')) mkdir('/tmp/cache', 0755, true);
    if (!is_dir('/tmp/cache/packages')) mkdir('/tmp/cache/packages', 0755, true);
}
// ===== AKHIR TAMBAHAN =====

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ...
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();
