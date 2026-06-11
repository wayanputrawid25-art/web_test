<?php

namespace App\Modules\Inventory\Presentation\Livewire;

use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Product\Infrastructure\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public function render()
    {
        $stockBalances = app(InventoryService::class)->getAllStockBalances();

        $recentTransactions = app(InventoryService::class)->getTransactions(
            new \App\Modules\Inventory\Application\DTOs\InventoryFilterDTO(['per_page' => 10])
        );

        $lowStock = $stockBalances->filter(fn ($item) => $item['current_stock'] <= 10)->count();

        $totalProducts = $stockBalances->count();
        $totalStock = $stockBalances->sum('current_stock');

        return view('inventory::dashboard', [
            'stockBalances' => $stockBalances->take(20),
            'recentTransactions' => $recentTransactions,
            'lowStock' => $lowStock,
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
        ]);
    }
}