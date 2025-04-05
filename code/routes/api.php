<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Auth\AuthLogoutController;
use App\Http\Controllers\RoleTestController;
use Illuminate\Support\Facades\Route;

Route::post('/login', AuthLoginController::class)->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', AuthLogoutController::class)->name('logout');

    Route::get('/test/superadmin', [RoleTestController::class, 'show'])->middleware('role:SuperAdmin');
    Route::get('/test/admin', [RoleTestController::class, 'show'])->middleware('role:Admin');
    Route::get('/test/employee', [RoleTestController::class, 'show'])->middleware('role:Employee');
    Route::get('/test/client', [RoleTestController::class, 'show'])->middleware('role:Client');

});
