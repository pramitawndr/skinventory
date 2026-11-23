<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\StockTransactionController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// Halaman yang membutuhkan login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    
// Produk: dapat dilihat semua pengguna yang login
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/export', [ProductController::class, 'export'])
    ->name('products.export');

// Transaksi Stok - dapat dilihat semua pengguna login
Route::get('/stock-transactions', [StockReportController::class, 'index'])
    ->name('stock-reports.index');

// Form dan pengiriman pengajuan - Admin
Route::middleware(['role:admin'])->group(function () {
    Route::get('/stock-transactions/create', [StockReportController::class, 'create'])
        ->name('stock-reports.create');

    Route::post('/stock-transactions', [StockReportController::class, 'store'])
        ->name('stock-reports.store');
});

// Persetujuan pengajuan - Auditor
Route::middleware(['role:auditor'])->group(function () {
    Route::post('/stock-reports/{stockReport}/approve', [StockReportController::class, 'approve'])
        ->name('stock-reports.approve');

    Route::post('/stock-reports/{stockReport}/reject', [StockReportController::class, 'reject'])
        ->name('stock-reports.reject');
});

// Riwayat transaksi final
Route::get('/transaction-history', [StockTransactionController::class, 'index'])
    ->name('stock-transactions.index');
    
// Produk: hanya Auditor yang boleh mengubah data
Route::middleware('role:auditor')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
});
});

// Batch: semua pengguna yang login dapat melihat
Route::get('/batches', [BatchController::class, 'index'])
    ->name('batches.index');

// Batch: hanya Auditor yang dapat mengelola
Route::middleware('role:auditor')->group(function () {
    Route::get('/batches/create', [BatchController::class, 'create'])
        ->name('batches.create');

    Route::post('/batches', [BatchController::class, 'store'])
        ->name('batches.store');

    Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])
        ->name('batches.edit');

    Route::put('/batches/{batch}', [BatchController::class, 'update'])
        ->name('batches.update');

    Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])
        ->name('batches.destroy');
});

Route::get('/test-auditor', function () {
    return 'Akses Auditor berhasil.';
})->middleware('role:auditor');