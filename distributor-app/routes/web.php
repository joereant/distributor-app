<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReferalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserManagementController;
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

        // User Management
        Route::get('/admin/users-manage', [UserManagementController::class, 'index']);
        Route::post('/admin/users-manage', [UserManagementController::class, 'store']);
        Route::put('/admin/users-manage/{user}', [UserManagementController::class, 'update']);
        Route::delete('/admin/users-manage/{user}', [UserManagementController::class, 'destroy']);

        // Customers
        Route::get('/admin/customers', [CustomerController::class, 'index']);
        Route::post('/admin/customers', [CustomerController::class, 'store']);
        Route::put('/admin/customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy']);

        // Products
        Route::get('/admin/products', [ProductController::class, 'index']);
        Route::post('/admin/products/prices', [ProductController::class, 'savePrices']);
        Route::get('/admin/products/create', [ProductController::class, 'create']);
        Route::post('/admin/products', [ProductController::class, 'store']);
        Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit']);
        Route::put('/admin/products/{product}', [ProductController::class, 'update']);
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);

        // Categories (nested under Products)
        Route::get('/admin/products/categories', [CategoryController::class, 'index']);
        Route::post('/admin/products/categories', [CategoryController::class, 'store']);
        Route::put('/admin/products/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/admin/products/categories/{category}', [CategoryController::class, 'destroy']);

        // Areas
        Route::get('/admin/areas', [AreaController::class, 'index']);
        Route::post('/admin/areas', [AreaController::class, 'store']);
        Route::put('/admin/areas/{area}', [AreaController::class, 'update']);
        Route::delete('/admin/areas/{area}', [AreaController::class, 'destroy']);

        // Plants
        Route::get('/admin/plants', [PlantController::class, 'index']);
        Route::post('/admin/plants', [PlantController::class, 'store']);
        Route::put('/admin/plants/{plant}', [PlantController::class, 'update']);
        Route::delete('/admin/plants/{plant}', [PlantController::class, 'destroy']);

        // Shipping Rates
        Route::get('/admin/shipping-rates', [ShippingRateController::class, 'index']);
        Route::post('/admin/shipping-rates', [ShippingRateController::class, 'store']);
        Route::put('/admin/shipping-rates/{shippingRate}', [ShippingRateController::class, 'update']);
        Route::delete('/admin/shipping-rates/{shippingRate}', [ShippingRateController::class, 'destroy']);

        // Reports
        Route::get('/admin/reports', [ReportController::class, 'index']);
        Route::get('/admin/reports/export', [ReportController::class, 'exportCsv']);
    });
    Route::middleware('role:sales')->get('/sales', [DashboardController::class, 'sales']);

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer', [DashboardController::class, 'customer']);
        Route::get('/customer/order', [OrderController::class, 'create']);
        Route::post('/customer/order', [OrderController::class, 'store']);
        Route::get('/customer/referal', [ReferalController::class, 'index']);
        Route::post('/customer/referal/regenerate', [ReferalController::class, 'regenerate']);
    });
});
