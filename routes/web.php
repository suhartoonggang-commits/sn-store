<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.public.home')->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin', 'can:dashboard.view'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])
            ->middleware('can:categories.view')
            ->name('categories.index');
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('can:products.view')
            ->name('products.index');
        Route::get('/transactions', [TransactionController::class, 'index'])
            ->middleware('can:transactions.view')
            ->name('transactions.index');
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
