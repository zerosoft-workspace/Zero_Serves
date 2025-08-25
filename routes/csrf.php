<?php

use Illuminate\Support\Facades\Route;

// CSRF token refresh endpoint for preventing 419 errors
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');
