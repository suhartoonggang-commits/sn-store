<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
});

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'show'])->name('api.cart.show');
    Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('api.cart.items.store');
});
