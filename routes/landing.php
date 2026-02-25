<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/produk/{productDisplay}', [LandingController::class, 'product'])->name('landing.product');
Route::get('/pembayaran/{saleId}', [LandingController::class, 'payment'])->name('landing.payment');
