<?php

namespace App\Modules\Inventory\Presentation\Livewire;

use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Inventory\Exceptions\InsufficientStockException;
use App\Modules\Product\Infrastructure\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockOut extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $productId = 0;
    public int $quantity = 0;
    public string $reference = '';
    public string $notes = '';

    public string $searchProduct = '';
    public array $selectedProduct = [];

    protected function rules(): array
    {
        return [
            'productId' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
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
                'type' => 'stock_out',
                'per_page' => 10,
            ])
        );

        $stockBalances = app(InventoryService::class)->getAllStockBalances();

        return view('inventory::stock-out', [
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
    }

    public function clearProduct(): void
    {
        $this->productId = 0;
        $this->selectedProduct = [];
    }

    public function store(): void
    {
        $this->validate();

        try {
            app(InventoryService::class)->stockOut([
                'product_id' => $this->productId,
                'quantity' => $this->quantity,
                'reference' => $this->reference ?: null,
                'notes' => $this->notes ?: null,
            ], auth()->id());

            $this->reset(['quantity', 'reference', 'notes']);
            $this->clearProduct();

            session()->flash('success', 'Stock Out berhasil disimpan');
        } catch (InsufficientStockException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}