<?php

namespace App\Modules\StockOpname\Presentation\Livewire;

use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\Product\Infrastructure\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockOpnameCreate extends Component
{
    public string $name = '';
    public string $description = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $countDeadline = null;
    public array $selectedProducts = [];
    public ?int $taskId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'countDeadline' => ['nullable', 'date', 'after_or_equal:today'],
            'selectedProducts' => ['required', 'array', 'min:1'],
            'selectedProducts.*' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama session wajib diisi',
            'selectedProducts.required' => 'Pilih minimal 1 produk',
            'selectedProducts.min' => 'Pilih minimal 1 produk',
            'endDate.after_or_equal' => 'End date harus setelah start date',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-stock-opnames');
    }

    public function render()
    {
        $products = Product::active()
            ->with('category')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'quantity' => $p->quantity,
                'category' => $p->category?->name,
            ]);

        return view('stock_opname::create', [
            'products' => $products,
        ]);
    }

    public function save(): void
    {
        $this->validate();

        try {
            $session = app(StockOpnameService::class)->createSession([
                'name' => $this->name,
                'description' => $this->description,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'count_deadline' => $this->countDeadline,
                'task_id' => $this->taskId,
                'product_ids' => $this->selectedProducts,
            ]);

            session()->flash('success', 'Stock Opname berhasil dibuat');

            $this->redirectRoute('stock_opnames.show', $session->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirectRoute('stock_opnames.index');
    }

    public function toggleProduct(int $productId): void
    {
        $key = array_search($productId, $this->selectedProducts);
        if ($key !== false) {
            unset($this->selectedProducts[$key]);
            $this->selectedProducts = array_values($this->selectedProducts);
        } else {
            $this->selectedProducts[] = $productId;
        }
    }

    public function selectAll(): void
    {
        $this->selectedProducts = \App\Modules\Product\Infrastructure\Models\Product::active()
            ->pluck('id')->toArray();
    }

    public function clearSelection(): void
    {
        $this->selectedProducts = [];
    }
}