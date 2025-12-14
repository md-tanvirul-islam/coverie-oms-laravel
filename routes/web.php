<?php

use App\Http\Controllers\CourierPaidInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
// use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'welcome'])->name("welcome");

// Route::middleware('auth')->group(function () {
//     Route::get('/me', [ProfileController::class, 'me'])->name('profile');
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name("dashboard");

    // Route::group(['prefix' => 'lw'], function () {    
    //     Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // });

    Route::resource('users', UserController::class);
    Route::resource('moderators', ModeratorController::class);

    Route::get('orders/import', [OrderController::class, 'import'])->name('orders.import');
    Route::post('orders/import', [OrderController::class, 'importStore'])->name('orders.import.store');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::resource('orders', OrderController::class);

    Route::get('courier_paid_invoices/import', [CourierPaidInvoiceController::class, 'import'])->name('courier_paid_invoices.import');
    Route::post('courier_paid_invoices/import', [CourierPaidInvoiceController::class, 'importStore'])->name('courier_paid_invoices.import.store');
    Route::get('courier_paid_invoices/export', [CourierPaidInvoiceController::class, 'export'])->name('courier_paid_invoices.export');
    Route::resource('courier_paid_invoices', CourierPaidInvoiceController::class);

    Route::get('stores/import', [StoreController::class, 'import'])->name('stores.import');
    Route::post('stores/import', [StoreController::class, 'importStore'])->name('stores.import.store');
    Route::get('stores/export', [StoreController::class, 'export'])->name('stores.export');
    Route::resource('stores', StoreController::class);


    Route::get('reports/moderator_commission/daily', [ReportController::class, 'moderatorCommissionDailyReport'])
        ->name('reports.moderator_commission.daily');

    Route::get('reports/moderator_commission/monthly', [ReportController::class, 'moderatorCommissionMonthlyReport'])
        ->name('reports.moderator_commission.monthly');
});

require __DIR__ . '/auth.php';
