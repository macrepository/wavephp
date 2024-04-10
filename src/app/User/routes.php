<?php

use Base\Router\Route;
use App\User\Controller\User;
use App\User\Controller\Index;

$user = new User();

Route::get('/user', function ($req) {
    $userIndex = new Index();
});

Route::post('/api/user', function ($req) use ($user) {
    return $user->createUser($req);
});

Route::get('/api/user/', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::get('/api/user/:id', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::patch('/api/user/:id', function ($req) use ($user){
    return $user->updateUser($req);
});

Route::delete('/api/user/:id', function ($req) use ($user) {
    return $user->deleteUser($req);
});