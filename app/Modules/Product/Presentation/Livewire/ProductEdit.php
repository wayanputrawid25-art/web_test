<?php

namespace App\Modules\Product\Presentation\Livewire;

use App\Modules\Product\Application\Services\ProductService;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ProductEdit extends Component
{
    public ?Product $product = null;
    public string $sku = '';
    public string $name = '';
    public string $category = '';
    public string $status = 'active';

    protected function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9\-]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    protected function messages(): array
    {
        return [
            'sku.required' => 'SKU wajib diisi',
            'sku.unique' => 'SKU sudah digunakan',
            'sku.regex' => 'SKU hanya boleh mengandung huruf kapital, angka, dan tanda hubung',
            'name.required' => 'Nama produk wajib diisi',
            'category.required' => 'Kategori wajib diisi',
            'status.required' => 'Status wajib dipilih',
        ];
    }

    public function mount(int $id): void
    {
        Gate::authorize('edit-products');

        $this->product = app(ProductService::class)->getProduct($id);

        $this->sku = $this->product->sku;
        $this->name = $this->product->name;
        $this->category = $this->product->category;
        $this->status = $this->product->status->value;
    }

    public function render()
    {
        return view('product::edit', [
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        try {
            app(ProductService::class)->updateProduct($this->product->id, [
                'sku' => strtoupper($this->sku),
                'name' => $this->name,
                'category' => $this->category,
                'status' => $this->status,
            ]);

            session()->flash('success', 'Produk berhasil diperbarui');

            $this->redirectRoute('products.index');
        } catch (\App\Modules\Product\Exceptions\DuplicateSkuException $e) {
            $this->addError('sku', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirectRoute('products.index');
    }
}