<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// ============================================================
// SOLUSI UNTUK VERCEL (READ-ONLY FILESYSTEM)
// ============================================================
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    // Buat direktori di /tmp
    $tmpStorage = '/tmp/storage';
    $tmpCache   = '/tmp/bootstrap/cache';
    $tmpManifest = '/tmp/packages.php'; // <-- TAMBAHKAN INI

    if (!is_dir($tmpStorage)) mkdir($tmpStorage, 0755, true);
    if (!is_dir($tmpStorage . '/logs')) mkdir($tmpStorage . '/logs', 0755, true);
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0755, true);

    // Set environment variables
    putenv("APP_STORAGE_PATH={$tmpStorage}");
    putenv("APP_BOOTSTRAP_CACHE_PATH={$tmpCache}");
    putenv("PACKAGE_MANIFEST_PATH={$tmpManifest}"); // <-- TAMBAHKAN INI
    $_ENV['APP_STORAGE_PATH'] = $tmpStorage;
    $_ENV['APP_BOOTSTRAP_CACHE_PATH'] = $tmpCache;
    $_ENV['PACKAGE_MANIFEST_PATH'] = $tmpManifest; // <-- TAMBAHKAN INI
    $_SERVER['APP_STORAGE_PATH'] = $tmpStorage;
    $_SERVER['APP_BOOTSTRAP_CACHE_PATH'] = $tmpCache;
    $_SERVER['PACKAGE_MANIFEST_PATH'] = $tmpManifest; // <-- TAMBAHKAN INI
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

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

$app->handleRequest(Request::capture());
