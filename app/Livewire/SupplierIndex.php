<?php

namespace App\Livewire;

use App\Models\Supplier\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $suppliers = Supplier::withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.supplier-index', compact('suppliers'))->layout('components.layouts.app');
    }

    public function toggleActive(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => !$supplier->is_active]);
    }

    public function deleteSupplier(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        
        if ($supplier->products()->count() > 0) {
            session()->flash('error', 'Cannot delete supplier with associated products.');
            return;
        }

        $supplier->delete();
        session()->flash('message', 'Supplier deleted successfully.');
    }
}