<?php

namespace App\Modules\TaskCenter\Presentation\Livewire;

use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Application\DTOs\ChangeStatusDTO;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\Entities\TaskActivityLog;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TaskView extends Component
{
    public ?Task $task = null;
    public array $activityLogs = [];
    public string $newComment = '';
    public string $statusNotes = '';

    protected $rules = [
        'newComment' => ['nullable', 'string', 'max:1000'],
        'statusNotes' => ['nullable', 'string', 'max:500'],
    ];

    public function mount(int $id): void
    {
        Gate::authorize('view-tasks');

        $data = app(TaskService::class)->getTaskWithLogs($id);
        $this->task = $data['task'];
        $this->activityLogs = $data['activityLogs']->toArray();
    }

    public function render()
    {
        return view('task_center::view');
    }

    public function changeStatus(string $newStatus): void
    {
        Gate::authorize('edit-tasks');

        try {
            app(TaskService::class)->changeTaskStatus($this->task->id, [
                'status' => $newStatus,
                'notes' => $this->statusNotes ?: null,
            ]);

            $this->refreshTask();

            $this->statusNotes = '';
            session()->flash('success', 'Status berhasil diubah');
        } catch (\App\Modules\TaskCenter\Exceptions\InvalidStatusTransitionException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function assignTo(int $userId): void
    {
        Gate::authorize('edit-tasks');

        app(TaskService::class)->assignTask($this->task->id, $userId);

        $this->refreshTask();

        session()->flash('success', 'Task berhasil di-assign');
    }

    public function addComment(): void
    {
        if (empty($this->newComment)) {
            return;
        }

        Gate::authorize('edit-tasks');

        app(TaskService::class)->addComment($this->task->id, $this->newComment);

        $this->refreshTask();

        $this->newComment = '';
        session()->flash('success', 'Komentar berhasil ditambahkan');
    }

    private function refreshTask(): void
    {
        $data = app(TaskService::class)->getTaskWithLogs($this->task->id);
        $this->task = $data['task'];
        $this->activityLogs = $data['activityLogs']->toArray();
    }

    public function getNextStatuses(): array
    {
        return $this->task->getNextStatuses();
    }

    public function back(): void
    {
        $this->redirectRoute('tasks.index');
    }
}