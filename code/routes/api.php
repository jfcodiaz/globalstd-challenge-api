<?php

use App\Http\Controllers\Auth\AuthLoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', AuthLoginController::class)->name('login');
/*
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});*/
