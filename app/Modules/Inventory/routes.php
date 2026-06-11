<?php

use App\Modules\Inventory\Presentation\Livewire\StockIn;
use App\Modules\Inventory\Presentation\Livewire\StockOut;
use App\Modules\Inventory\Presentation\Livewire\StockAdjustment;
use App\Modules\Inventory\Presentation\Livewire\StockLedger;
use App\Modules\Inventory\Presentation\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    Route::prefix('stock-in')->name('stock-in.')->group(function () {
        Route::get('/', StockIn::class)->name('index');
        Route::post('/', StockIn::class)->name('store');
    });

    Route::prefix('stock-out')->name('stock-out.')->group(function () {
        Route::get('/', StockOut::class)->name('index');
        Route::post('/', StockOut::class)->name('store');
    });

    Route::prefix('adjustment')->name('adjustment.')->group(function () {
        Route::get('/', StockAdjustment::class)->name('index');
        Route::post('/', StockAdjustment::class)->name('store');
    });

    Route::get('/ledger', StockLedger::class)->name('ledger');
});