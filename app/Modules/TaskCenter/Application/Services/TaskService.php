<?php

namespace App\Modules\TaskCenter\Application\Services;

use App\Modules\TaskCenter\Application\Actions\TaskAction;
use App\Modules\TaskCenter\Application\DTOs\CreateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\UpdateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\ChangeStatusDTO;
use App\Modules\TaskCenter\Application\DTOs\TaskFilterDTO;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\Entities\TaskActivityLog;
use App\Modules\TaskCenter\Exceptions\InvalidStatusTransitionException;
use App\Modules\TaskCenter\Exceptions\TaskNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskService
{
    public function __construct(
        private readonly TaskAction $action
    ) {}

    public function getTasks(TaskFilterDTO $filter): LengthAwarePaginator
    {
        return $this->action->getAll($filter->toArray());
    }

    public function getTask(int $id): Task
    {
        return $this->action->getById($id);
    }

    public function createTask(array $data): Task
    {
        $dto = CreateTaskDTO::fromArray($data);

        return $this->action->create($dto);
    }

    public function updateTask(int $id, array $data): Task
    {
        $dto = UpdateTaskDTO::fromArray($data);

        return $this->action->update($id, $dto);
    }

    public function deleteTask(int $id): bool
    {
        return $this->action->delete($id);
    }

    public function assignTask(int $taskId, int $assigneeId): Task
    {
        return $this->action->assign($taskId, $assigneeId);
    }

    public function changeTaskStatus(int $taskId, array $data): Task
    {
        $dto = ChangeStatusDTO::fromArray($data);

        return $this->action->changeStatus($taskId, $dto);
    }

    public function getTaskActivityLogs(int $taskId): Collection
    {
        return $this->action->getActivityLogs($taskId);
    }

    public function getTaskWithLogs(int $taskId): array
    {
        return $this->action->getTaskWithLogs($taskId);
    }

    public function getStatusCounts(): array
    {
        return $this->action->getStatusCounts();
    }

    public function addComment(int $taskId, string $comment): TaskActivityLog
    {
        return $this->action->addComment($taskId, $comment);
    }
}