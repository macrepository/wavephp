<?php

use Base\Router\Route;
use App\User\Controller\User;

Route::get('/api/user', function ($req) {
    $user = new User();
    return $user->getUser($req);
});
