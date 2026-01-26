<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// ========== GUEST ROUTES (Hanya untuk yang belum login) ==========
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    // Register Routes
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// ========== AUTHENTICATED ROUTES (Hanya untuk yang sudah login) ==========
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    
    // Password Update
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');
});