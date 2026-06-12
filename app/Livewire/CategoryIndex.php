<?php

namespace App\Livewire;

use App\Models\Category\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.category-index', compact('categories'))->layout('components.layouts.app');
    }

    public function toggleActive(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::findOrFail($id);
        
        if ($category->products()->count() > 0) {
            session()->flash('error', 'Cannot delete category with associated products.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
    }
}