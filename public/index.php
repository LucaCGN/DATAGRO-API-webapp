<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve JavaScript files with the correct MIME type
//if (preg_match('/\.(?:js)$/', $_SERVER["REQUEST_URI"])) {
//    header("Content-Type: application/javascript");
//    readfile(__DIR__ . $_SERVER["REQUEST_URI"]);
//    exit;
//}

// Check If The Application Is Under Maintenance
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register The Auto Loader
require __DIR__.'/../vendor/autoload.php';

// Run The Application
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
