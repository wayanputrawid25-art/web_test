<?php

namespace App\Modules\TaskCenter\Presentation\Livewire;

use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Application\DTOs\TaskFilterDTO;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class TaskIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $statusFilter = null;
    public ?string $priorityFilter = null;
    public int $perPage = 15;
    public bool $myTasks = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
        'priorityFilter' => ['except' => null],
        'myTasks' => ['except' => false],
        'perPage' => ['except' => 15],
    ];

    public function mount(bool $myTasksOnly = false): void
    {
        Gate::authorize('view-tasks');
        $this->myTasks = $myTasksOnly;
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => $this->statusFilter,
            'priority' => $this->priorityFilter,
            'my_tasks' => $this->myTasks ?: null,
        ]);

        $tasks = app(TaskService::class)->getTasks(
            TaskFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $statusCounts = app(TaskService::class)->getStatusCounts();

        return view('task_center::index', [
            'tasks' => $tasks,
            'statusCounts' => $statusCounts,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
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

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'priorityFilter']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-tasks');

        app(TaskService::class)->deleteTask($id);

        $this->dispatch('task-deleted');
        session()->flash('success', 'Task berhasil dihapus');
    }
}