<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'welcome'])->name("welcome");

Route::middleware('auth')->group(function () {
    Route::get('/me', [ProfileController::class, 'me'])->name('profile');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name("dashboard");
    
    Route::resource('users', UserController::class);
    Route::resource('moderators', ModeratorController::class);
    Route::resource('orders', OrderController::class);
    // Excel upload/download
    Route::post('orders/import', [OrderController::class, 'import'])->name('orders.import');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
});

require __DIR__ . '/auth.php';
