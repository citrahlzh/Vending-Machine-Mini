<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\PackagingTypeController;
use App\Http\Controllers\API\PackagingSizeController;
use App\Http\Controllers\API\CellController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PriceController;
use App\Http\Controllers\API\ProductDisplayController;
use App\Http\Controllers\API\AdController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix'=>'user'], function () {
    Route::get('/list', [UserController::class, 'index']);
    Route::post('/store', [UserController::class, 'store']);
    Route::get('/show/{id}', [UserController::class, 'show']);
    Route::get('/edit/{id}', [UserController::class, 'edit']);
    Route::post('/update/{id}', [UserController::class, 'update']);
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix'=>'category'], function () {
    Route::get('/list', [CategoryController::class, 'index']);
    Route::post('/store', [CategoryController::class, 'store']);
    Route::get('/show/{id}', [CategoryController::class, 'show']);
    Route::get('/edit/{id}', [CategoryController::class, 'edit']);
    Route::post('/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
});

Route::group(['prefix'=>'brand'], function () {
    Route::get('/list', [BrandController::class, 'index']);
    Route::post('/store', [BrandController::class, 'store']);
    Route::get('/show/{id}', [BrandController::class, 'show']);
    Route::get('/edit/{id}', [BrandController::class, 'edit']);
    Route::post('/update/{id}', [BrandController::class, 'update']);
    Route::delete('/delete/{id}', [BrandController::class, 'destroy']);
});

Route::group(['prefix'=>'packaging-type'], function () {
    Route::get('/list', [PackagingTypeController::class, 'index']);
    Route::post('/store', [PackagingTypeController::class, 'store']);
    Route::get('/show/{id}', [PackagingTypeController::class, 'show']);
    Route::get('/edit/{id}', [PackagingTypeController::class, 'edit']);
    Route::post('/update/{id}', [PackagingTypeController::class, 'update']);
    Route::delete('/delete/{id}', [PackagingTypeController::class, 'destroy']);
});

Route::group(['prefix'=>'packaging-size'], function () {
    Route::get('/list', [PackagingSizeController::class, 'index']);
    Route::post('/store', [PackagingSizeController::class, 'store']);
    Route::get('/show/{id}', [PackagingSizeController::class, 'show']);
    Route::get('/edit/{id}', [PackagingSizeController::class, 'edit']);
    Route::post('/update/{id}', [PackagingSizeController::class, 'update']);
    Route::delete('/delete/{id}', [PackagingSizeController::class, 'destroy']);
});

Route::group(['prefix'=>'product'], function () {
    Route::get('/list', [ProductController::class, 'index']);
    Route::post('/store', [ProductController::class, 'store']);
    Route::get('/show/{id}', [ProductController::class, 'show']);
    Route::get('/edit/{id}', [ProductController::class, 'edit']);
    Route::post('/update/{id}', [ProductController::class, 'update']);
    Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
});

Route::group(['prefix'=>'price'], function () {
    Route::get('/list', [PriceController::class, 'index']);
    Route::post('/store', [PriceController::class, 'store']);
    Route::get('/show/{id}', [PriceController::class, 'show']);
    Route::get('/edit/{id}', [PriceController::class, 'edit']);
    Route::post('/update/{id}', [PriceController::class, 'update']);
    Route::delete('/delete/{id}', [PriceController::class, 'destroy']);
});

Route::group(['prefix'=>'cell'], function () {
    Route::get('/list', [CellController::class, 'index']);
    Route::post('/store', [CellController::class, 'store']);
    Route::get('/show/{id}', [CellController::class, 'show']);
    Route::get('/edit/{id}', [CellController::class, 'edit']);
    Route::post('/update/{id}', [CellController::class, 'update']);
    Route::delete('/delete/{id}', [CellController::class, 'destroy']);
});

Route::group(['prefix'=>'product-display'], function () {
    Route::get('/list', [ProductDisplayController::class, 'index']);
    Route::post('/store', [ProductDisplayController::class, 'store']);
    Route::get('/show/{id}', [ProductDisplayController::class, 'show']);
    Route::get('/edit/{id}', [ProductDisplayController::class, 'edit']);
    Route::post('/update/{id}', [ProductDisplayController::class, 'update']);
    Route::delete('/delete/{id}', [ProductDisplayController::class, 'destroy']);
});

Route::group(['prefix'=>'ad'], function () {
    Route::get('/list', [AdController::class, 'index']);
    Route::post('/store', [AdController::class, 'store']);
    Route::get('/show/{id}', [AdController::class, 'show']);
    Route::get('/edit/{id}', [AdController::class, 'edit']);
    Route::post('/update/{id}', [AdController::class, 'update']);
    Route::delete('/delete/{id}', [AdController::class, 'destroy']);
});
