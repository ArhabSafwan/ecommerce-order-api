<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;

Route::prefix('v1')->group(function () {

    // Auth public
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');

    // token refresh
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    // Note: you may instead use 'jwt.refresh' middleware provided by package or simply allow refresh to attempt refresh token

    // Protected routes
    Route::middleware(['auth:api'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Admin-only routes
        Route::middleware(['role:admin'])->group(function () {
            // Admin routes: manage users, view all orders, etc.
            Route::get('admin/dashboard', function () {
                return response()->json(['msg' => 'admin area']);
            });
        });

        // Vendor routes
        Route::middleware(['role:vendor'])->group(function () {
            // manage own products, orders
            Route::get('vendor/dashboard', function () {
                return response()->json(['msg' => 'vendor area']);
            });
        });

        // Customer routes
        Route::middleware(['role:customer'])->group(function () {
            Route::get('customer/dashboard', function () {
                return response()->json(['msg' => 'customer area']);
            });
        });
    });
    
    // Product routes (accessible to authenticated users)
    Route::middleware(['auth:api'])->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::post('products', [ProductController::class, 'store']); // vendor/admin
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);

        Route::post('products/import-csv', [ProductController::class, 'importCsv']); // vendor/admin
        Route::post('products/decrease-inventory', [ProductController::class, 'decreaseInventory']);
        Route::get('products/search', [ProductController::class, 'search']);
    });
});
