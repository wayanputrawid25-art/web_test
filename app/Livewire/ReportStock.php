<?php

namespace App\Livewire;

use App\Models\Product\Product;
use Livewire\Component;

class ReportStock extends Component
{
    public function render()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('current_stock');
        $totalValue = Product::sum(\DB::raw('current_stock * cost_price'));
        $lowStockCount = Product::whereColumn('current_stock', '<=', 'min_stock')->count();
        $outOfStockCount = Product::where('current_stock', '<=', 0)->count();
        $topProducts = Product::orderBy('current_stock', 'desc')->take(10)->get();

        return view('livewire.report-stock', compact(
            'totalProducts', 'totalStock', 'totalValue',
            'lowStockCount', 'outOfStockCount', 'topProducts'
        ))->layout('components.layouts.app');
    }
}