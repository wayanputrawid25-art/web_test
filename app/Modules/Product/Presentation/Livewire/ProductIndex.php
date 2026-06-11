<?php

namespace App\Modules\Product\Presentation\Livewire;

use App\Modules\Product\Application\Services\ProductService;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Presentation\Requests\CreateProductRequest;
use App\Modules\Product\Presentation\Requests\UpdateProductRequest;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ProductIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $statusFilter = null;
    public ?string $categoryFilter = null;
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
        'categoryFilter' => ['except' => null],
        'perPage' => ['except' => 15],
    ];

    public function mount(): void
    {
        Gate::authorize('view-products');
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => $this->statusFilter,
            'category' => $this->categoryFilter,
        ]);

        $products = app(ProductService::class)->getProducts(
            \App\Modules\Product\Application\DTOs\ProductFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $categories = $this->getCategories();

        return view('product::index', [
            'products' => $products,
            'categories' => $categories,
            'statuses' => ProductStatus::cases(),
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

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'categoryFilter']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-products');

        app(ProductService::class)->deleteProduct($id);

        $this->dispatch('product-deleted');
        session()->flash('success', 'Produk berhasil dihapus');
    }

    public function toggleStatus(int $id): void
    {
        Gate::authorize('edit-products');

        app(ProductService::class)->toggleProductStatus($id);

        $this->dispatch('product-updated');
        session()->flash('success', 'Status produk berhasil diubah');
    }

    private function getCategories(): array
    {
        return \App\Modules\Product\Infrastructure\Models\Product::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }
}