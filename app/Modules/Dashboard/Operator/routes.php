<?php

use App\Modules\Dashboard\Operator\Presentation\Livewire\OperatorDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/operator', OperatorDashboard::class)->name('operator');
    Route::get('/', OperatorDashboard::class)->name('index');
});