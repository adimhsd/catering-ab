<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Pengadaan Catering Al-Bahjah
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Route yang membutuhkan autentikasi (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard — dapat diakses semua role
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Profile (bawaan Breeze)
    Route::view('/profile', 'profile')->name('profile.edit');
    Route::view('/profile-view', 'profile')->name('profile');

    /*
    |--------------------------------------------------------------------------
    | Laporan & Analitik — dapat diakses semua role (Admin Dapur + Kepala Divisi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', \App\Livewire\Report\PurchaseReport::class)->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
    });

    Route::prefix('analitik')->name('analytics.')->group(function () {
        Route::get('/', \App\Livewire\Analytics\AnalyticsDashboard::class)->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Riwayat Pembelian — dapat dilihat semua role
    |--------------------------------------------------------------------------
    */
    Route::prefix('pembelian')->name('purchases.')->group(function () {
        Route::get('/', \App\Livewire\Transaction\PurchaseList::class)->name('index');
        Route::get('/{purchase}', \App\Livewire\Transaction\PurchaseShow::class)->name('show')->whereNumber('purchase');
    });

    /*
    |--------------------------------------------------------------------------
    | Master Data & Transaksi — hanya Admin Dapur
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin_dapur'])->group(function () {

        // Transaksi Pembelian (create/edit/delete)
        Route::get('/pembelian/buat', \App\Livewire\Transaction\PurchaseCreate::class)->name('purchases.create');
        Route::get('/pembelian/{purchase}/edit', \App\Livewire\Transaction\PurchaseEdit::class)->name('purchases.edit')->whereNumber('purchase');
        Route::delete('/pembelian/{purchase}', [ReportController::class, 'deletePurchase'])->name('purchases.destroy');

        // Master Supplier
        Route::prefix('supplier')->name('suppliers.')->group(function () {
            Route::get('/', \App\Livewire\Supplier\SupplierManager::class)->name('index');
        });

        // Master Produk
        Route::prefix('produk')->name('products.')->group(function () {
            Route::get('/', \App\Livewire\Product\ProductManager::class)->name('index');
        });

        // Master Kategori & Satuan
        Route::prefix('kategori-satuan')->name('categories.')->group(function () {
            Route::get('/', \App\Livewire\MasterData\CategoryUnitManager::class)->name('index');
        });
    });
});

require __DIR__.'/auth.php';
