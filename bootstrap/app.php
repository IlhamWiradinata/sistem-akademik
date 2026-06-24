<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Buat aplikasi
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// ============================================================
// OVERRIDE PATH UNTUK VERCEL (SEBELUM SERVICE PROVIDER DIREGISTER)
// ============================================================
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    // Buat direktori di /tmp (jika belum ada)
    $tmpBootstrap = '/tmp/bootstrap';
    $tmpCache = $tmpBootstrap . '/cache';
    $tmpStorage = '/tmp/storage';

    if (!is_dir($tmpBootstrap)) mkdir($tmpBootstrap, 0755, true);
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0755, true);
    if (!is_dir($tmpStorage)) mkdir($tmpStorage, 0755, true);
    if (!is_dir($tmpStorage . '/logs')) mkdir($tmpStorage . '/logs', 0755, true);
    if (!is_dir($tmpCache . '/views')) mkdir($tmpCache . '/views', 0755, true);

    // Path override
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
}

return $app;
