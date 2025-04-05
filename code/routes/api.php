<?php

use App\Http\Controllers\Auth\AuthLoginController;
use App\Http\Controllers\Auth\AuthLogoutController;
use App\Http\Controllers\RoleTestController;
use App\Http\Controllers\User\UserDeleteController;
use App\Http\Controllers\User\UserStatusController;
use Illuminate\Support\Facades\Route;

Route::post('/login', AuthLoginController::class)->name('login');

Route::middleware(['auth:sanctum', 'block_inactive', 'preload_roles'])->group(function () {

    Route::post('/logout', AuthLogoutController::class)->name('logout');

    // Test routes by role
    Route::get('/test/superadmin', [RoleTestController::class, 'show'])->middleware('role:SuperAdmin');
    Route::get('/test/admin', [RoleTestController::class, 'show'])->middleware('role:Admin');
    Route::get('/test/employee', [RoleTestController::class, 'show'])->middleware('role:Employee');
    Route::get('/test/client', [RoleTestController::class, 'show'])->middleware('role:Client');

    // Routes only accessible by SuperAdmin and Admin
    Route::middleware('role:SuperAdmin,Admin')->group(function () {
        Route::patch('/users/{user}/status', UserStatusController::class);
        Route::delete('/users/{user}', UserDeleteController::class);
    });
});
