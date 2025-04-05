<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Auth\AuthLogoutController;
use Illuminate\Support\Facades\Route;

Route::post('/login', AuthLoginController::class)->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', AuthLogoutController::class)->name('logout');
});
