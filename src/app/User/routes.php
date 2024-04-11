<?php

use Base\Router\Route;
use App\User\Controller\User;
use App\User\Controller\Login;
use App\User\Controller\Register;
use App\User\Controller\Account;

$user = new User();

// WEB PAGES ROUTES

Route::get('/user/login', function ($req) {
    $user = new Login($req);
    $user->create();
});

Route::post('/user/login', function ($req) {
    $user = new Login($req);
    $user->post();
});

Route::get('/user/register', function ($req) {
    $user = new Register($req);
    $user->create();
});

Route::post('/user/register', function ($req) {
    $user = new Register($req);
    $user->post();
});

Route::get('/user/account', function ($req) {
    $user = new Account($req);
    $user->create();
});

Route::get('/user/logout', function ($req) {
    $user = new Account($req);
    $user->logout();
});


// USER API ENDPOINTS

Route::post('/api/user/login', function ($req) use ($user) {
    return $user->loginUser($req);
});

Route::get('/api/user/account', function ($req) use ($user) {
    return $user->userAccount($req);
});

Route::get('/api/user/logout', function ($req) use ($user) {
    return $user->userLogout();
});

// CRUD OPERATION API ENDPOINTS

Route::post('/api/user', function ($req) use ($user) {
    return $user->createUser($req);
});

Route::get('/api/user/', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::get('/api/user/:id', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::patch('/api/user/:id', function ($req) use ($user) {
    return $user->updateUser($req);
});

Route::delete('/api/user/:id', function ($req) use ($user) {
    return $user->deleteUser($req);
});
