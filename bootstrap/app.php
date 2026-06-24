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
// OVERRIDE PATH UNTUK VERCEL (READ-ONLY FILESYSTEM)
// ============================================================
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $tmpBootstrap = '/tmp/bootstrap';
    $tmpCache = $tmpBootstrap . '/cache';
    $tmpStorage = '/tmp/storage';

    // Buat semua direktori yang diperlukan
    if (!is_dir($tmpBootstrap)) mkdir($tmpBootstrap, 0755, true);
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0755, true);
    if (!is_dir($tmpStorage)) mkdir($tmpStorage, 0755, true);
    if (!is_dir($tmpStorage . '/logs')) mkdir($tmpStorage . '/logs', 0755, true);
    if (!is_dir($tmpStorage . '/framework')) mkdir($tmpStorage . '/framework', 0755, true);
    if (!is_dir($tmpStorage . '/framework/views')) mkdir($tmpStorage . '/framework/views', 0755, true); // <-- PENTING!
    if (!is_dir($tmpStorage . '/framework/cache')) mkdir($tmpStorage . '/framework/cache', 0755, true);
    if (!is_dir($tmpStorage . '/framework/sessions')) mkdir($tmpStorage . '/framework/sessions', 0755, true);

    // Override path di Laravel
    $app->useBootstrapPath($tmpBootstrap);
    $app->useStoragePath($tmpStorage);
}

return $app;
