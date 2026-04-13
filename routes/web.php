<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang Masuk
    Route::get('/stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::get('/stock-in/create', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');
    
    // Barang Keluar
    Route::get('/stock-out', [StockOutController::class, 'index'])->name('stock-out.index');
    Route::get('/stock-out/create', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');
    
    // API Fetch untuk Filter Rak Otomatis
    Route::get('/api/get-raks-by-product/{productId}', [StockOutController::class, 'getRaksByProduct']);

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('raks', RakController::class);
        Route::resource('users', UserController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        
        Route::get('/stock-in/{id}/edit', [StockInController::class, 'edit'])->name('stock-in.edit');
        Route::put('/stock-in/{id}', [StockInController::class, 'update'])->name('stock-in.update');

        Route::get('/stock-out/{id}/edit', [StockOutController::class, 'edit'])->name('stock-out.edit');
        Route::put('/stock-out/{id}', [StockOutController::class, 'update'])->name('stock-out.update');
    });
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});