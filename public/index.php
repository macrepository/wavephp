<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("BP", dirname(__DIR__));

require BP . '/vendor/autoload.php';

use Base\Views\Render;
use Base\Router\Route;
use Dotenv\Dotenv;

// Load Dotenv
$dotenv = Dotenv::createImmutable(BP);
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'])->notEmpty();

// Load routes
$directory = BP . '/src/app/';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getFilename() == 'routes.php') {
        require $file->getPathname();
    }
}

if (Route::isRestApi()) {
    header('Content-Type: application/json; charset=utf-8');
    Route::run();
} else {
    Render::views('Page/view/page', ['routerView' => Route::class]);
}
