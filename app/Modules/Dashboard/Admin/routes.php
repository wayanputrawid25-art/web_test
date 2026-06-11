<?php

use App\Modules\Dashboard\Admin\Presentation\Livewire\AdminDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:SuperAdmin|WarehouseAdmin'])->prefix('dashboard/admin')->name('dashboard.admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('index');
});