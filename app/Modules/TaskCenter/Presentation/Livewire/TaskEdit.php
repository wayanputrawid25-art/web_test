<?php

namespace App\Modules\TaskCenter\Presentation\Livewire;

use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\Users\Infrastructure\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class TaskEdit extends Component
{
    public ?Task $task = null;
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
            'dueDate' => ['nullable', 'date'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul task wajib diisi',
            'assigneeId.required' => 'Assignee wajib dipilih',
        ];
    }

    public function mount(int $id): void
    {
        Gate::authorize('edit-tasks');

        $this->task = app(TaskService::class)->getTask($id);

        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->priority = $this->task->priority->value;
        $this->assigneeId = $this->task->assigneeId;
        $this->productId = $this->task->productId;
        $this->inventoryTransactionId = $this->task->inventoryTransactionId;
        $this->dueDate = $this->task->dueDate?->format('Y-m-d');
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

        return view('task_center::edit', [
            'users' => $users,
            'products' => $products,
            'priorities' => TaskPriority::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        app(TaskService::class)->updateTask($this->task->id, [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assignee_id' => $this->assigneeId,
            'product_id' => $this->productId,
            'inventory_transaction_id' => $this->inventoryTransactionId,
            'due_date' => $this->dueDate,
        ]);

        session()->flash('success', 'Task berhasil diperbarui');

        $this->redirectRoute('tasks.index');
    }

    public function cancel(): void
    {
        $this->redirectRoute('tasks.index');
    }
}