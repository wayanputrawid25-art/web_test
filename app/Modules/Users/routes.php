<?php

use App\Modules\Users\Presentation\Livewire\UserCreate;
use App\Modules\Users\Presentation\Livewire\UserEdit;
use App\Modules\Users\Presentation\Livewire\UserIndex;
use App\Modules\Users\Presentation\Livewire\UserView;
use App\Modules\Users\Presentation\Livewire\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', UserIndex::class)->name('index');
    Route::get('/create', UserCreate::class)->name('create');
    Route::get('/{id}', UserView::class)->name('show')->where('id', '[0-9]+');
    Route::get('/{id}/edit', UserEdit::class)->name('edit')->where('id', '[0-9]+');
    Route::get('/{id}/roles', UserRole::class)->name('roles')->where('id', '[0-9]+');
});