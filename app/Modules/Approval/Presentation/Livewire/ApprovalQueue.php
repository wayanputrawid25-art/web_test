<?php

namespace App\Modules\Approval\Presentation\Livewire;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\Approval\Application\DTOs\ApprovalDecisionDTO;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ApprovalQueue extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $queueType = 'my';
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(string $queueType = 'my'): void
    {
        Gate::authorize('view-approvals');
        Gate::authorize('edit-approvals');

        $this->queueType = $queueType;
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => 'pending',
            'pending_for_me' => true,
        ]);

        if ($this->queueType === 'all') {
            unset($filters['pending_for_me']);
        }

        $requests = app(ApprovalService::class)->getRequests(
            ApprovalFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $pendingCount = app(ApprovalService::class)->getQueueCounts()['pending'] ?? 0;

        return view('approval::queue', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function quickApprove(int $id): void
    {
        Gate::authorize('edit-approvals');

        try {
            app(ApprovalService::class)->approve($id, [
                'comments' => 'Quick approved from queue',
            ]);

            session()->flash('success', 'Request approved');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function quickReject(int $id, string $reason): void
    {
        Gate::authorize('edit-approvals');

        if (empty($reason)) {
            session()->flash('error', 'Reason is required for rejection');
            return;
        }

        try {
            app(ApprovalService::class)->reject($id, [
                'comments' => $reason,
            ]);

            session()->flash('success', 'Request rejected');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}