<?php

namespace App\Modules\TaskCenter\Domain\Contracts;

use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\Entities\TaskActivityLog;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function findById(int $id): ?Task;
    
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    public function create(array $data): Task;
    
    public function update(int $id, array $data): Task;
    
    public function delete(int $id): bool;
    
    public function findByAssignee(int $assigneeId, array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    public function findByStatus(TaskStatus $status, array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    public function getActivityLogs(int $taskId): Collection;
    
    public function createActivityLog(array $data): TaskActivityLog;
    
    public function countByStatus(): array;
}