<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Buat direktori di /tmp (agar bisa ditulis)
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $tmpBootstrap = '/tmp/bootstrap';
    $tmpCache = $tmpBootstrap . '/cache';
    $tmpStorage = '/tmp/storage';

    if (!is_dir($tmpBootstrap)) mkdir($tmpBootstrap, 0755, true);
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0755, true);
    if (!is_dir($tmpStorage)) mkdir($tmpStorage, 0755, true);
    if (!is_dir($tmpStorage . '/logs')) mkdir($tmpStorage . '/logs', 0755, true);
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Override storage path juga (sebagai cadangan)
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

$app->handleRequest(Request::capture());
