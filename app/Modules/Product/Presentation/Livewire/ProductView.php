<?php

namespace App\Modules\Product\Presentation\Livewire;

use App\Modules\Product\Application\Services\ProductService;
use App\Modules\Product\Domain\Entities\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ProductView extends Component
{
    public ?Product $product = null;

    public function mount(int $id): void
    {
        Gate::authorize('view-products');

        $this->product = app(ProductService::class)->getProduct($id);
    }

    public function render()
    {
        return view('product::view');
    }

    public function back(): void
    {
        $this->redirectRoute('products.index');
    }
}