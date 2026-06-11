<?php

namespace App\Modules\Product\Presentation\Livewire;

use App\Modules\Product\Application\Services\ProductService;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Presentation\Requests\CreateProductRequest;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class ProductCreate extends Component
{
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
            'status' => ['sometimes', 'in:active,inactive'],
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
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-products');
    }

    public function render()
    {
        return view('product::create', [
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        try {
            app(ProductService::class)->createProduct([
                'sku' => strtoupper($this->sku),
                'name' => $this->name,
                'category' => $this->category,
                'status' => $this->status,
            ]);

            session()->flash('success', 'Produk berhasil ditambahkan');

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