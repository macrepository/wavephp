<?php

use Base\Router\Route;
use App\User\Controller\User;
use App\User\Controller\Index;

Route::get('/user', function ($req) {
    $userIndex = new Index();
});

Route::get('/api/user', function ($req) {
    $user = new User();
    return $user->getUser($req);
});
