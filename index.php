<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("BP", __DIR__);

require BP . '/vendor/autoload.php';

use Base\Views\Render;
use Base\Router\Route;

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
    Render::views('Theme/view/page', ['routerView' => Route::class]);
}
