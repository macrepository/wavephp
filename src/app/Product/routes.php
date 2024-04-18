<?php

use Base\Router\Route;
use App\Product\Controller\Product;


// Product Pages
Route::get('/product', function ($req) {
    $product = new Product();
    $product->listProductView();
});

Route::get('/product/add', function ($req) {
    $product = new Product();
    $product->addProductView();
});

Route::get('/product/edit/:id', function ($req) {
    $product = new Product();
    $product->updateProductView($req);
});


// Create
Route::post('/product/add', function ($req) {
    $product = new Product();
    $product->addProduct($req);
});

// Update
Route::post('/product/edit/:id', function ($req) {
    $product = new Product();
    $product->updateProduct($req);
});


// REST API

// Fetch
Route::get('/api/product', function ($req) {
    $product = new Product();
    return $product->getProduct($req);
});

// Delete
Route::delete('/api/product/:id', function ($req) {
    $product = new Product();
    return $product->deleteProduct($req);
});
