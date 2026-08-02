<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:owner')->get('/owner', [DashboardController::class, 'owner']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'admin']);
        Route::get('/admin/orders', [OrderController::class, 'index']);
        Route::post('/admin/orders/{transaction}/approve', [OrderController::class, 'approve']);
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::post('/admin/users/{user}/approve', [UserController::class, 'approve']);
        Route::post('/admin/users/{user}/reject', [UserController::class, 'reject']);

        // Products
        Route::get('/admin/products', [ProductController::class, 'index']);
        Route::post('/admin/products/prices', [ProductController::class, 'savePrices']);
        Route::post('/admin/products/categories', [ProductController::class, 'saveCategory']);
        Route::post('/admin/products/categories/{category}/toggle', [ProductController::class, 'toggleCategory']);
        Route::delete('/admin/products/categories/{category}', [ProductController::class, 'deleteCategory']);
        Route::get('/admin/products/create', [ProductController::class, 'create']);
        Route::post('/admin/products', [ProductController::class, 'store']);
        Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit']);
        Route::put('/admin/products/{product}', [ProductController::class, 'update']);
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
    });
    Route::middleware('role:sales')->get('/sales', [DashboardController::class, 'sales']);

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer', [DashboardController::class, 'customer']);
        Route::get('/customer/order', [OrderController::class, 'create']);
        Route::post('/customer/order', [OrderController::class, 'store']);
    });
});
