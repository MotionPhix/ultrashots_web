<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Entry point for the split-root layout on 50webs.
 *
 * The document root is www/ultrashots.net, but the application lives beside it
 * in www/shared/ultrashots. Laravel's own public/index.php reaches one level up
 * for the autoloader and the bootstrap, which from this document root resolves
 * to www/vendor — a directory that does not exist — so the app would fatal on
 * every request. Both paths are therefore pointed at the shared application.
 *
 * Keep this file in step with public/index.php in the repository: same
 * maintenance check, same bootstrap, same request capture.
 */
define('LARAVEL_START', microtime(true));

$appPath = __DIR__.'/../shared/ultrashots';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appPath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
