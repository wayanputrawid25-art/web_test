<?php

use App\Modules\Approval\Presentation\Livewire\ApprovalIndex;
use App\Modules\Approval\Presentation\Livewire\ApprovalCreate;
use App\Modules\Approval\Presentation\Livewire\ApprovalView;
use App\Modules\Approval\Presentation\Livewire\ApprovalQueue;
use App\Modules\Approval\Presentation\Livewire\ApprovalHistory;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('approvals')->name('approvals.')->group(function () {
    // Main routes
    Route::get('/', ApprovalIndex::class)->name('index');
    Route::get('/create', ApprovalCreate::class)->name('create');
    Route::get('/{id}', ApprovalView::class)->name('show')->where('id', '[0-9]+');

    // Queue routes (for approvers)
    Route::get('/queue/my', ApprovalQueue::class)->name('queue.my')->defaults('queue_type', 'my');
    Route::get('/queue/all', ApprovalQueue::class)->name('queue.all')->defaults('queue_type', 'all');

    // History routes
    Route::get('/history/my', ApprovalHistory::class)->name('history.my')->defaults('history_type', 'my');
    Route::get('/history/all', ApprovalHistory::class)->name('history.all')->defaults('history_type', 'all');
});