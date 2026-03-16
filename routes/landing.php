<?php

use App\Http\Controllers\Api\GamePlayController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Landing\GameController;
use App\Http\Controllers\Landing\GameResultController;

Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/produk/{productDisplay}', [LandingController::class, 'product'])->name('landing.product');
Route::get('/pembayaran/{saleId}', [LandingController::class, 'payment'])->name('landing.payment');

Route::prefix('games')->name('games.')->group(function () {
    Route::get('/', [GameController::class, 'index'])->name('index');
    Route::get('/quiz/{game}', [GameController::class, 'quiz'])->name('quiz');
    Route::get('/guess-image/{game}', [GameController::class, 'guessImage'])->name('guess-image');
    Route::get('/spin-wheel/{game}', [GameController::class, 'spinWheel'])->name('spin-wheel');
    Route::post('/spin/{game}', [GamePlayController::class, 'spin'])->middleware('throttle:20,1');
    Route::get('/play/{game}', [GameController::class, 'play'])->name('play');

    Route::get('/result/success/{issuedReward}', [GameResultController::class, 'success'])
        ->middleware('signed')
        ->name('result.success');

    Route::get('/result/fail', [GameResultController::class, 'fail'])
        ->name('result.fail');
});
