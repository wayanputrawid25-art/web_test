<?php

namespace App\Modules\TaskCenter\Application\Actions;

use App\Modules\TaskCenter\Application\DTOs\CreateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\UpdateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\ChangeStatusDTO;
use App\Modules\TaskCenter\Domain\Contracts\TaskRepositoryInterface;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\Entities\TaskActivityLog;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use App\Modules\TaskCenter\Exceptions\InvalidStatusTransitionException;
use App\Modules\TaskCenter\Exceptions\TaskNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskAction
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->findAll($filters);
    }

    public function getById(int $id): Task
    {
        $task = $this->repository->findById($id);

        if (!$task) {
            throw new TaskNotFoundException($id);
        }

        return $task;
    }

    public function create(CreateTaskDTO $dto): Task
    {
        $task = $this->repository->create($dto->toArray());

        $this->logActivity($task->id, 'created', null, $task->status->value, auth()->id());

        return $task;
    }

    public function update(int $id, UpdateTaskDTO $dto): Task
    {
        $task = $this->repository->findById($id);

        if (!$task) {
            throw new TaskNotFoundException($id);
        }

        $task = $this->repository->update($id, $dto->toArray());

        $this->logActivity($task->id, 'updated', null, null, auth()->id(), 'Task details updated');

        return $task;
    }

    public function delete(int $id): bool
    {
        $task = $this->repository->findById($id);

        if (!$task) {
            throw new TaskNotFoundException($id);
        }

        return $this->repository->delete($id);
    }

    public function assign(int $taskId, int $assigneeId): Task
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        $oldAssignee = $task->assigneeId;
        $task = $this->repository->update($taskId, ['assignee_id' => $assigneeId]);

        // Log the assignment
        $this->logActivity(
            $task->id,
            $oldAssignee ? 'reassigned' : 'assigned',
            (string) $oldAssignee,
            (string) $assigneeId,
            auth()->id()
        );

        // If task is draft, auto-transition to assigned
        if ($task->status->isDraft()) {
            return $this->changeStatus($taskId, ChangeStatusDTO::fromArray(['status' => 'assigned']));
        }

        return $task;
    }

    public function changeStatus(int $taskId, ChangeStatusDTO $dto): Task
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        $newStatus = TaskStatus::from($dto->newStatus);

        if (!$task->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException($task->status->value, $newStatus->value);
        }

        $oldStatus = $task->status->value;
        $task = $this->repository->update($taskId, ['status' => $newStatus->value]);

        $this->logActivity(
            $task->id,
            'status_changed',
            $oldStatus,
            $newStatus->value,
            auth()->id(),
            $dto->notes
        );

        return $task;
    }

    public function getActivityLogs(int $taskId): Collection
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        return $this->repository->getActivityLogs($taskId);
    }

    public function getTaskWithLogs(int $taskId): array
    {
        $task = $this->getById($taskId);
        $logs = $this->getActivityLogs($taskId);

        return [
            'task' => $task,
            'activityLogs' => $logs,
        ];
    }

    public function getStatusCounts(): array
    {
        return $this->repository->countByStatus();
    }

    public function addComment(int $taskId, string $comment): TaskActivityLog
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        return $this->logActivity($taskId, 'commented', null, null, auth()->id(), $comment);
    }

    private function logActivity(
        int $taskId,
        string $action,
        ?string $oldValue,
        ?string $newValue,
        int $userId,
        ?string $notes = null
    ): TaskActivityLog {
        return $this->repository->createActivityLog([
            'task_id' => $taskId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }
}