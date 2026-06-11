<?php

namespace App\Modules\TaskCenter\Presentation\Livewire;

use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\Users\Infrastructure\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class TaskCreate extends Component
{
    public string $title = '';
    public string $description = '';
    public string $priority = 'medium';
    public int $assigneeId;
    public ?int $productId = null;
    public ?int $inventoryTransactionId = null;
    public ?string $dueDate = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'assigneeId' => ['required', 'integer', 'exists:users,id'],
            'productId' => ['nullable', 'integer', 'exists:products,id'],
            'inventoryTransactionId' => ['nullable', 'integer', 'exists:inventory_transactions,id'],
            'dueDate' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul task wajib diisi',
            'assigneeId.required' => 'Assignee wajib dipilih',
            'assigneeId.exists' => 'User tidak ditemukan',
            'productId.exists' => 'Product tidak ditemukan',
            'inventoryTransactionId.exists' => 'Inventory transaction tidak ditemukan',
            'dueDate.after_or_equal' => 'Due date tidak boleh kurang dari hari ini',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-tasks');
    }

    public function render()
    {
        $users = User::active()->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        $products = \App\Modules\Product\Infrastructure\Models\Product::active()
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
            ]);

        return view('task_center::create', [
            'users' => $users,
            'products' => $products,
            'priorities' => TaskPriority::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        app(TaskService::class)->createTask([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assignee_id' => $this->assigneeId,
            'creator_id' => auth()->id(),
            'product_id' => $this->productId,
            'inventory_transaction_id' => $this->inventoryTransactionId,
            'due_date' => $this->dueDate,
        ]);

        session()->flash('success', 'Task berhasil dibuat');

        $this->redirectRoute('tasks.index');
    }

    public function cancel(): void
    {
        $this->redirectRoute('tasks.index');
    }
}