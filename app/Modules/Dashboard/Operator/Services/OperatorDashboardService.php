<?php

namespace App\Modules\Dashboard\Operator\Services;

use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Application\DTOs\TaskFilterDTO;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Application\DTOs\StockOpnameFilterDTO;
use App\Modules\Users\Infrastructure\Models\User;
use Illuminate\Support\Collection;

class OperatorDashboardService
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly StockOpnameService $stockOpnameService
    ) {}

    public function getOperatorData(int $userId): array
    {
        return [
            'user' => $this->getOperatorInfo($userId),
            'tasks' => $this->getTodayTasks($userId),
            'progress' => $this->getProgressStats($userId),
            'activeOpname' => $this->getActiveOpname($userId),
        ];
    }

    public function getOperatorInfo(int $userId): ?array
    {
        $user = User::find($userId);

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'warehouse' => $user->warehouse ?? 'Main Warehouse',
            'roles' => $user->getRoleNames()->toArray(),
            'last_login' => $user->last_login_at?->diffForHumans(),
        ];
    }

    public function getTodayTasks(int $userId): array
    {
        $filters = TaskFilterDTO::fromArray([
            'assignee_id' => $userId,
            'status' => 'pending',
        ]);

        $tasks = $this->taskService->getTasks($filters);

        $upcomingTasks = $tasks->filter(function ($task) {
            if (!$task->dueDate) {
                return true;
            }
            return $task->dueDate->isToday() || $task->dueDate->isFuture();
        });

        $overdueTasks = $tasks->filter(function ($task) {
            if (!$task->dueDate) {
                return false;
            }
            return $task->dueDate->isPast() && !$task->dueDate->isToday();
        });

        return [
            'total' => $tasks->total(),
            'upcoming' => $upcomingTasks->values()->toArray(),
            'overdue' => $overdueTasks->values()->toArray(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
            ],
        ];
    }

    public function getProgressStats(int $userId): array
    {
        // Get completed tasks this week
        $completedTasksFilter = TaskFilterDTO::fromArray([
            'assignee_id' => $userId,
            'status' => 'completed',
        ]);
        $completedTasks = $this->taskService->getTasks($completedTasksFilter);

        // Get items counted from stock opname assignments
        $opnameFilter = StockOpnameFilterDTO::fromArray([
            'my_assignments' => true,
        ]);
        $opnameSessions = $this->stockOpnameService->getSessions($opnameFilter);

        $totalItemsCounted = 0;
        $totalVarianceFound = 0;

        foreach ($opnameSessions as $session) {
            $items = $this->stockOpnameService->getItems($session->id);
            foreach ($items as $item) {
                if ($item->countedQuantity !== null) {
                    $totalItemsCounted++;
                    if ($item->hasVariance()) {
                        $totalVarianceFound++;
                    }
                }
            }
        }

        // Calculate overall progress
        $totalAssignedTasks = $completedTasks->total() + $tasks->total();
        $completedTaskCount = $completedTasks->total();
        $taskProgressPercentage = $totalAssignedTasks > 0 
            ? round(($completedTaskCount / $totalAssignedTasks) * 100) 
            : 0;

        return [
            'completed_tasks' => $completedTaskCount,
            'pending_tasks' => $tasks->total(),
            'items_counted' => $totalItemsCounted,
            'variance_found' => $totalVarianceFound,
            'task_progress_percentage' => $taskProgressPercentage,
            'opname_sessions_count' => $opnameSessions->total(),
        ];
    }

    public function getActiveOpname(int $userId): ?array
    {
        // Get sessions where user is assigned and status is counting or assigned
        $filters = StockOpnameFilterDTO::fromArray([
            'my_assignments' => true,
            'status' => 'counting',
        ]);

        $countingSessions = $this->stockOpnameService->getSessions($filters);

        if ($countingSessions->total() > 0) {
            $session = $countingSessions->first();
            return $this->formatActiveOpname($session);
        }

        // Check for assigned sessions
        $assignedFilters = StockOpnameFilterDTO::fromArray([
            'my_assignments' => true,
            'status' => 'assigned',
        ]);

        $assignedSessions = $this->stockOpnameService->getSessions($assignedFilters);

        if ($assignedSessions->total() > 0) {
            $session = $assignedSessions->first();
            return $this->formatActiveOpname($session);
        }

        // Check for submitted sessions (revision requested)
        $revisionFilters = StockOpnameFilterDTO::fromArray([
            'my_assignments' => true,
            'status' => 'submitted',
        ]);

        $revisionSessions = $this->stockOpnameService->getSessions($revisionFilters);

        if ($revisionSessions->total() > 0) {
            $session = $revisionSessions->first();
            return $this->formatActiveOpname($session);
        }

        return null;
    }

    private function formatActiveOpname($session): array
    {
        $items = $this->stockOpnameService->getItems($session->id);
        
        $countedItems = $items->filter(fn ($item) => $item->countedQuantity !== null)->count();
        $totalItems = $items->count();
        $progressPercentage = $totalItems > 0 ? round(($countedItems / $totalItems) * 100) : 0;

        return [
            'id' => $session->id,
            'code' => $session->code,
            'name' => $session->name,
            'status' => $session->status->value,
            'status_label' => $session->status->label(),
            'status_color' => $session->status->color(),
            'progress_percentage' => $progressPercentage,
            'counted_items' => $countedItems,
            'total_items' => $totalItems,
            'variance_count' => $items->filter(fn ($item) => $item->hasVariance())->count(),
            'deadline' => $session->countDeadline?->format('Y-m-d'),
            'deadline_label' => $session->countDeadline?->diffForHumans(),
            'is_overdue' => $session->countDeadline ? $session->countDeadline->isPast() : false,
            'can_continue' => in_array($session->status->value, ['assigned', 'counting', 'submitted']),
        ];
    }

    public function getRecentActivity(int $userId, int $limit = 10): array
    {
        // Get recent task activity
        $taskFilters = TaskFilterDTO::fromArray([
            'assignee_id' => $userId,
        ]);
        $tasks = $this->taskService->getTasks($taskFilters);

        // Get recent opname activity
        $opnameFilters = StockOpnameFilterDTO::fromArray([
            'my_assignments' => true,
        ]);
        $sessions = $this->stockOpnameService->getSessions($opnameFilters);

        // Combine and sort by date
        $activities = [];

        foreach ($tasks as $task) {
            $activities[] = [
                'type' => 'task',
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status->value,
                'created_at' => $task->createdAt,
            ];
        }

        foreach ($sessions as $session) {
            $activities[] = [
                'type' => 'stock_opname',
                'id' => $session->id,
                'title' => $session->name,
                'status' => $session->status->value,
                'created_at' => $session->createdAt,
            ];
        }

        usort($activities, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return array_slice($activities, 0, $limit);
    }

    public function getQuickStats(int $userId): array
    {
        return [
            'tasks_today' => count($this->getTodayTasks($userId)['upcoming']),
            'tasks_overdue' => count($this->getTodayTasks($userId)['overdue']),
            'active_opname' => $this->getActiveOpname($userId) !== null,
            'pending_approvals' => 0, // Future: integrate with Approval module
        ];
    }
}