<?php

use Illuminate\Support\Facades\Route;

//menambahkan Semua route auth
require __DIR__ . '/auth.php';

// menambahkan Semua route landing
require __DIR__ . '/landing.php';

// menambahkan Semua route dashboard
require __DIR__ . '/dashboard.php';

// Route get image from storage
Route::get('/image/{path}', function ($path) {
    $path = storage_path("app/public/" . $path);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->where('path', '.*');
