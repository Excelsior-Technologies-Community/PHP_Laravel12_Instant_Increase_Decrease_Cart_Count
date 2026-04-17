<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;



Route::get('/', [ProductController::class,'index'])->name('products.index');
Route::get('/products/create', [ProductController::class,'create'])->name('products.create');
Route::post('/products/store', [ProductController::class,'store'])->name('products.store');

// Cart routes
Route::get('/cart/add/{id}', [CartController::class,'add']);
Route::get('/cart/update/{id}/{type}', [CartController::class,'update']);
Route::get('/cart/remove/{id}', [CartController::class,'remove']);
Route::get('/cart/checkout', [CartController::class,'checkout'])->name('cart.checkout');

// Dashboard
Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');