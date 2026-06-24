<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// ============================================================
// SOLUSI UNTUK VERCEL (READ-ONLY FILESYSTEM)
// ============================================================
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    // Tentukan path baru di /tmp
    $tmpBootstrap = '/tmp/bootstrap';
    $tmpCache     = $tmpBootstrap . '/cache';
    $tmpStorage   = '/tmp/storage';

    // Buat direktori
    if (!is_dir($tmpBootstrap)) mkdir($tmpBootstrap, 0755, true);
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0755, true);
    if (!is_dir($tmpStorage)) mkdir($tmpStorage, 0755, true);
    if (!is_dir($tmpStorage . '/logs')) mkdir($tmpStorage . '/logs', 0755, true);

    // Set environment variables
    putenv("APP_STORAGE_PATH={$tmpStorage}");
    putenv("APP_BOOTSTRAP_CACHE_PATH={$tmpCache}");
    putenv("PACKAGE_MANIFEST_PATH={$tmpCache}/packages.php");
    $_ENV['APP_STORAGE_PATH'] = $tmpStorage;
    $_ENV['APP_BOOTSTRAP_CACHE_PATH'] = $tmpCache;
    $_ENV['PACKAGE_MANIFEST_PATH'] = $tmpCache . '/packages.php';
    $_SERVER['APP_STORAGE_PATH'] = $tmpStorage;
    $_SERVER['APP_BOOTSTRAP_CACHE_PATH'] = $tmpCache;
    $_SERVER['PACKAGE_MANIFEST_PATH'] = $tmpCache . '/packages.php';
}

// ============================================================
// LARAVEL BOOTSTRAP STANDAR
// ============================================================
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// ============================================================
// OVERRIDE PATH SETELAH APP DIBUAT
// ============================================================
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
    // Tambahkan juga untuk memastikan cache path
    $app->make('config')->set('view.compiled', '/tmp/bootstrap/cache/views');
    $app->make('config')->set('cache.stores.file.path', '/tmp/storage/framework/cache');
}

$app->handleRequest(Request::capture());
