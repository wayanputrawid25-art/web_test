<?php

use App\Livewire\CategoryIndex;
use App\Livewire\Dashboard;
use App\Livewire\RoleIndex;
use App\Livewire\ReportStock;
use App\Livewire\SupplierIndex;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard (root)
    Route::get('/', Dashboard::class)->name('dashboard');

    // Roles (not in existing modules)
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', RoleIndex::class)->name('index');
    });

    // Categories (not in existing modules)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', CategoryIndex::class)->name('index');
    });

    // Suppliers (not in existing modules)
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', SupplierIndex::class)->name('index');
    });

    // Reports (not in existing modules)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', ReportStock::class)->name('index');
        Route::get('/stock', ReportStock::class)->name('stock');
    });
});