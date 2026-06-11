<?php

use App\Modules\StockOpname\Presentation\Livewire\StockOpnameIndex;
use App\Modules\StockOpname\Presentation\Livewire\StockOpnameCreate;
use App\Modules\StockOpname\Presentation\Livewire\StockOpnameView;
use App\Modules\StockOpname\Presentation\Livewire\StockOpnameCount;
use App\Modules\StockOpname\Presentation\Livewire\StockOpnameAssign;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('stock-opnames')->name('stock_opnames.')->group(function () {
    Route::get('/', StockOpnameIndex::class)->name('index');
    Route::get('/create', StockOpnameCreate::class)->name('create');
    Route::get('/my-tasks', StockOpnameIndex::class)->name('my-tasks')->defaults('my_assignments', true);
    Route::get('/{id}', StockOpnameView::class)->name('show')->where('id', '[0-9]+');
    Route::get('/{id}/count', StockOpnameCount::class)->name('count')->where('id', '[0-9]+');
    Route::get('/{id}/assign', StockOpnameAssign::class)->name('assign')->where('id', '[0-9]+');
});