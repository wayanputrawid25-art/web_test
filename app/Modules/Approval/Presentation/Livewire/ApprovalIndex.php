<?php

namespace App\Modules\Approval\Presentation\Livewire;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ApprovalIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $typeFilter = null;
    public ?string $statusFilter = null;
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => null],
        'statusFilter' => ['except' => null],
    ];

    public function mount(): void
    {
        Gate::authorize('view-approvals');
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'type' => $this->typeFilter,
            'status' => $this->statusFilter,
        ]);

        $requests = app(ApprovalService::class)->getRequests(
            ApprovalFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $statusCounts = app(ApprovalService::class)->getStatusCounts();

        return view('approval::index', [
            'requests' => $requests,
            'statusCounts' => $statusCounts,
            'statuses' => ApprovalStatus::cases(),
            'types' => ApprovalType::cases(),
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'typeFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-approvals');

        try {
            app(ApprovalService::class)->deleteRequest($id);
            session()->flash('success', 'Approval request deleted');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}