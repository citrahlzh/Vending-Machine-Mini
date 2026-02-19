<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CellController;
use App\Http\Controllers\Dashboard\PackagingSizeController;
use App\Http\Controllers\Dashboard\PackagingTypeController;
use App\Http\Controllers\Dashboard\PriceController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProductDisplayController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\AdController;
use App\Http\Controllers\Dashboard\NotificationController;

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/list', [NotificationController::class, 'index'])->name('list');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::post('/read/{id}', [NotificationController::class, 'markRead'])->name('read');
    });

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });

    Route::prefix('product-displays')->name('product-displays.')->group(function () {
        Route::get('/', [ProductDisplayController::class, 'index'])->name('index');
        Route::get('/create', [ProductDisplayController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [ProductDisplayController::class, 'edit'])->name('edit');
        Route::get('/{id}', [ProductDisplayController::class, 'show'])->name('show');
    });

    Route::prefix('prices')->name('prices.')->group(function () {
        Route::get('/', [PriceController::class, 'index'])->name('index');
        Route::get('/create', [PriceController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [PriceController::class, 'edit'])->name('edit');
        Route::get('/{id}', [PriceController::class, 'show'])->name('show');
    });

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    });

    Route::prefix('report')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
    });

    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', [DashboardController::class, 'masterIndex'])->name('index');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
        });

        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
        });

        Route::prefix('cells')->name('cells.')->group(function () {
            Route::get('/', [CellController::class, 'index'])->name('index');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
        });

        Route::prefix('packaging-types')->name('packaging-types.')->group(function () {
            Route::get('/', [PackagingTypeController::class, 'index'])->name('index');
        });

        Route::prefix('packaging-sizes')->name('packaging-sizes.')->group(function () {
            Route::get('/', [PackagingSizeController::class, 'index'])->name('index');
        });

        Route::prefix('ads')->name('ads.')->group(function () {
            Route::get('/', [AdController::class, 'index'])->name('index');
        });
    });
});

