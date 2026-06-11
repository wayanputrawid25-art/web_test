<?php

use App\Modules\TaskCenter\Presentation\Livewire\TaskIndex;
use App\Modules\TaskCenter\Presentation\Livewire\TaskCreate;
use App\Modules\TaskCenter\Presentation\Livewire\TaskEdit;
use App\Modules\TaskCenter\Presentation\Livewire\TaskView;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', TaskIndex::class)->name('index');
    Route::get('/create', TaskCreate::class)->name('create');
    Route::get('/my-tasks', TaskIndex::class)->name('my-tasks');
    Route::get('/{id}', TaskView::class)->name('show')->where('id', '[0-9]+');
    Route::get('/{id}/edit', TaskEdit::class)->name('edit')->where('id', '[0-9]+');
});