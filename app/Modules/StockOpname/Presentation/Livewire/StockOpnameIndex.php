<?php

namespace App\Modules\StockOpname\Presentation\Livewire;

use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Application\DTOs\StockOpnameFilterDTO;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockOpnameIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $statusFilter = null;
    public int $perPage = 15;
    public bool $myAssignments = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
        'myAssignments' => ['except' => false],
        'perPage' => ['except' => 15],
    ];

    public function mount(bool $myAssignmentsOnly = false): void
    {
        Gate::authorize('view-stock-opnames');
        $this->myAssignments = $myAssignmentsOnly;
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => $this->statusFilter,
            'my_assignments' => $this->myAssignments ?: null,
        ]);

        $sessions = app(StockOpnameService::class)->getSessions(
            StockOpnameFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $statusCounts = app(StockOpnameService::class)->getStatusCounts();

        return view('stock_opname::index', [
            'sessions' => $sessions,
            'statusCounts' => $statusCounts,
            'statuses' => StockOpnameStatus::cases(),
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-stock-opnames');

        try {
            app(StockOpnameService::class)->deleteSession($id);
            session()->flash('success', 'Stock Opname berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}