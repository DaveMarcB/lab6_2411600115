<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryTransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('transactions', InventoryTransactionController::class)->except(['edit', 'update']);

    // Part 6: Mark all low stock notifications as read
    Route::post('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('notifications.markAllRead');

    // Part 7: Real-Time Dashboard API Endpoints
    Route::get('/api/dashboard/stats', [DashboardController::class, 'apiStats'])->name('api.dashboard.stats');
    Route::get('/api/products/low-stock', [DashboardController::class, 'apiLowStock'])->name('api.products.low-stock');
    Route::get('/api/transactions/recent', [DashboardController::class, 'apiRecentTransactions'])->name('api.transactions.recent');

    // Part 8: Stock Adjustments & Product Stock AJAX
    Route::get('/adjustments/create', [InventoryTransactionController::class, 'createAdjustment'])->name('adjustments.create');
    Route::post('/adjustments', [InventoryTransactionController::class, 'storeAdjustment'])->name('adjustments.store');
    Route::get('/api/products/{product}/stock', [ProductController::class, 'apiProductStock'])->name('api.products.stock');

    // Part 9: Inventory Reports & CSV Export
    Route::get('/reports', [InventoryTransactionController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export-csv', [InventoryTransactionController::class, 'exportCsv'])->name('reports.export-csv');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';