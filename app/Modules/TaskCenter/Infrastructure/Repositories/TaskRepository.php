<?php

namespace App\Modules\TaskCenter\Infrastructure\Repositories;

use App\Modules\TaskCenter\Domain\Contracts\TaskRepositoryInterface;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\Entities\TaskActivityLog;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use App\Modules\TaskCenter\Infrastructure\Models\Task as TaskModel;
use App\Modules\TaskCenter\Infrastructure\Models\TaskActivityLog as ActivityLogModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function findById(int $id): ?Task
    {
        $model = TaskModel::with(['assignee', 'creator', 'product', 'inventoryTransaction'])
            ->find($id);

        return $model ? Task::fromModel($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TaskModel::with(['assignee', 'creator', 'product']);

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority']) && $filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assignee_id']) && $filters['assignee_id']) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        if (isset($filters['creator_id']) && $filters['creator_id']) {
            $query->where('creator_id', $filters['creator_id']);
        }

        if (isset($filters['product_id']) && $filters['product_id']) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        if (isset($filters['my_tasks']) && $filters['my_tasks']) {
            $query->where('assignee_id', auth()->id());
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function create(array $data): Task
    {
        $model = TaskModel::create($data);

        return $this->findById($model->id);
    }

    public function update(int $id, array $data): Task
    {
        $model = TaskModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $model = TaskModel::findOrFail($id);

        return $model->delete();
    }

    public function findByAssignee(int $assigneeId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['assignee_id'] = $assigneeId;

        return $this->findAll($filters, $perPage);
    }

    public function findByStatus(TaskStatus $status, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['status'] = $status->value;

        return $this->findAll($filters, $perPage);
    }

    public function getActivityLogs(int $taskId): Collection
    {
        return ActivityLogModel::with('user')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => TaskActivityLog::fromModel($model));
    }

    public function createActivityLog(array $data): TaskActivityLog
    {
        $model = ActivityLogModel::create($data);

        return TaskActivityLog::fromModel($model);
    }

    public function countByStatus(): array
    {
        $counts = TaskModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }
}