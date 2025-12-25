<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Default welcome page
Route::get('/', function () {
    return view('welcome'); // Loads the default Laravel welcome view
});

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // Show all products
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // Show form to create a new product
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store'); // Store new product in database

// Cart Routes
Route::get('/cart/add/{id}', [CartController::class,'add']); // Add product to cart via AJAX
Route::get('/cart/update/{id}/{type}', [CartController::class,'update']); // Update cart quantity (increase/decrease) via AJAX
Route::get('/cart/remove/{id}', [CartController::class,'remove']); // Remove product from cart via AJAX
