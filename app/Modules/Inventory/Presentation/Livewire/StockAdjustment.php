<?php

namespace App\Modules\Inventory\Presentation\Livewire;

use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Product\Infrastructure\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockAdjustment extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $productId = 0;
    public int $quantity = 0;
    public string $reference = '';
    public string $notes = '';
    public string $adjustmentType = 'add';

    public string $searchProduct = '';
    public array $selectedProduct = [];
    public int $currentStock = 0;

    protected function rules(): array
    {
        return [
            'productId' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjustmentType' => ['required', 'in:add,reduce'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-inventory');
    }

    public function render()
    {
        $products = Product::query()
            ->where('status', 'active')
            ->when($this->searchProduct, fn ($q) => $q->where('name', 'ilike', "%{$this->searchProduct}%")
                ->orWhere('sku', 'ilike', "%{$this->searchProduct}%"))
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'sku' => $p->sku, 'name' => $p->name])
            ->toArray();

        $recentTransactions = app(InventoryService::class)->getTransactions(
            new \App\Modules\Inventory\Application\DTOs\InventoryFilterDTO([
                'type' => 'adjustment',
                'per_page' => 10,
            ])
        );

        $stockBalances = app(InventoryService::class)->getAllStockBalances();

        return view('inventory::stock-adjustment', [
            'products' => $products,
            'recentTransactions' => $recentTransactions,
            'stockBalances' => $stockBalances,
        ]);
    }

    public function selectProduct(array $product): void
    {
        $this->productId = $product['id'];
        $this->selectedProduct = $product;
        $this->searchProduct = '';
        $this->currentStock = app(InventoryService::class)->getStockBalance($product['id']);
    }

    public function clearProduct(): void
    {
        $this->productId = 0;
        $this->selectedProduct = [];
        $this->currentStock = 0;
    }

    public function store(): void
    {
        $this->validate();

        $quantity = $this->adjustmentType === 'reduce' ? -$this->quantity : $this->quantity;

        try {
            app(InventoryService::class)->adjustStock([
                'product_id' => $this->productId,
                'quantity' => $quantity,
                'reference' => $this->reference ?: null,
                'notes' => $this->notes ?: null,
            ], auth()->id());

            $this->reset(['quantity', 'reference', 'notes', 'adjustmentType']);
            $this->clearProduct();

            session()->flash('success', 'Stock Adjustment berhasil disimpan');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}