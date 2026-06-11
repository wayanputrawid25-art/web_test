<?php

namespace App\Modules\Inventory\Presentation\Livewire;

use App\Modules\Inventory\Application\Services\InventoryService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockLedger extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?int $productFilter = null;
    public int $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'productFilter' => ['except' => null],
        'perPage' => ['except' => 25],
    ];

    public function mount(): void
    {
        Gate::authorize('view-inventory');
    }

    public function render()
    {
        $filter = new \App\Modules\Inventory\Application\DTOs\InventoryFilterDTO([
            'search' => $this->search ?: null,
            'product_id' => $this->productFilter,
            'per_page' => $this->perPage,
        ]);

        $ledger = app(InventoryService::class)->getLedger($this->perPage);

        $stockBalances = app(InventoryService::class)->getAllStockBalances();

        return view('inventory::ledger', [
            'ledger' => $ledger,
            'stockBalances' => $stockBalances,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingProductFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'productFilter']);
        $this->resetPage();
    }
}