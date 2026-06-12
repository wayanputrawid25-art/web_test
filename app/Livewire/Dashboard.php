<?php

namespace App\Livewire;

use App\Models\Product\Product;
use App\Models\StockIn\PurchaseOrder;
use App\Models\StockOut\StockOut;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalProducts = 0;
    public int $totalUsers = 0;
    public int $totalStockIn = 0;
    public int $totalStockOut = 0;
    public int $lowStockCount = 0;
    public int $pendingOrders = 0;
    public int $pendingDispatches = 0;
    public array $recentActivity = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'lowStockProducts' => Product::where('is_active', true)
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->orderBy('current_stock')
                ->take(10)
                ->get(),
        ])->layout('components.layouts.app');
    }

    public function refresh(): void
    {
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $this->totalProducts = Product::where('is_active', true)->count();
        $this->totalUsers = User::count();
        $this->totalStockIn = PurchaseOrder::whereIn('status', ['received', 'partial'])->count();
        $this->totalStockOut = StockOut::whereIn('status', ['dispatched', 'partial'])->count();
        $this->lowStockCount = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->count();
        $this->pendingOrders = PurchaseOrder::where('status', 'pending')->count();
        $this->pendingDispatches = StockOut::where('status', 'pending')->count();
    }
}