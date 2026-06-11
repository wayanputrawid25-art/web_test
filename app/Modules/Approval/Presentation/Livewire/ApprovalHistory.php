<?php

namespace App\Modules\Approval\Presentation\Livewire;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ApprovalHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $statusFilter = null;
    public string $historyType = 'my';
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
    ];

    public function mount(string $historyType = 'my'): void
    {
        Gate::authorize('view-approvals');

        $this->historyType = $historyType;
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => $this->statusFilter,
        ]);

        if ($this->historyType === 'my') {
            $filters['my_requests'] = true;
        }

        $requests = app(ApprovalService::class)->getRequests(
            ApprovalFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        return view('approval::history', [
            'requests' => $requests,
            'statuses' => ApprovalStatus::cases(),
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }
}