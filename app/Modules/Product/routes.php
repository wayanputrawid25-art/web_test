<?php

use App\Modules\Product\Presentation\Livewire\ProductCreate;
use App\Modules\Product\Presentation\Livewire\ProductEdit;
use App\Modules\Product\Presentation\Livewire\ProductIndex;
use App\Modules\Product\Presentation\Livewire\ProductView;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', ProductIndex::class)->name('index');
        Route::get('/create', ProductCreate::class)->name('create');
        Route::get('/{id}', ProductView::class)->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/edit', ProductEdit::class)->name('edit')->where('id', '[0-9]+');
    });
});