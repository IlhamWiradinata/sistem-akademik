<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tentukan lokasi folder Laravel (misal di dalam folder "resource")
$appPath = __DIR__.'/../resource';

// Maintenance mode
if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload
require $appPath.'/vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once $appPath.'/bootstrap/app.php';

// Handle request
$app->handleRequest(Request::capture());
