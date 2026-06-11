<?php

namespace App\Modules\Dashboard\Admin\Services;

use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Application\DTOs\StockOpnameFilterDTO;
use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\TaskCenter\Application\DTOs\TaskFilterDTO;
use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Inventory\Application\DTOs\InventoryFilterDTO;
use App\Modules\Product\Application\Services\ProductService;
use App\Modules\Users\Infrastructure\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminDashboardService
{
    public function __construct(
        private readonly ApprovalService $approvalService,
        private readonly StockOpnameService $stockOpnameService,
        private readonly TaskService $taskService,
        private readonly InventoryService $inventoryService,
        private readonly ProductService $productService
    ) {}

    public function getDashboardData(): array
    {
        return [
            'pendingApprovals' => $this->getPendingApprovals(),
            'activeStockOpnames' => $this->getActiveStockOpnames(),
            'taskSummary' => $this->getTaskSummary(),
            'inventorySummary' => $this->getInventorySummary(),
            'userActivity' => $this->getUserActivity(),
            'quickActions' => $this->getQuickActions(),
        ];
    }

    // Pending Approvals
    public function getPendingApprovals(): array
    {
        $filters = ApprovalFilterDTO::fromArray(['status' => 'pending', 'per_page' => 5]);
        $approvals = $this->approvalService->getRequests($filters);
        
        $counts = $this->approvalService->getStatusCounts();

        return [
            'pending_count' => $counts['pending'] ?? 0,
            'recent_requests' => $approvals->items(),
            'total_pending' => $approvals->total(),
        ];
    }

    public function getRecentApprovalRequests(int $limit = 5): array
    {
        $filters = ApprovalFilterDTO::fromArray([
            'status' => 'pending',
            'per_page' => $limit,
        ]);
        
        return $this->approvalService->getRequests($filters)->items();
    }

    // Active Stock Opnames
    public function getActiveStockOpnames(): array
    {
        $filters = StockOpnameFilterDTO::fromArray([
            'status' => 'in_progress',
            'per_page' => 5,
        ]);
        $sessions = $this->stockOpnameService->getSessions($filters);

        $counts = $this->stockOpnameService->getStatusCounts();

        $formattedSessions = [];
        foreach ($sessions as $session) {
            $items = $this->stockOpnameService->getItems($session->id);
            $formattedSessions[] = [
                'id' => $session->id,
                'code' => $session->code,
                'name' => $session->name,
                'status' => $session->status->value,
                'status_label' => $session->status->label(),
                'progress_percentage' => $session->getProgressPercentage(),
                'total_items' => $session->itemCount,
                'counted_items' => $session->countedCount,
                'variance_count' => $session->varianceCount,
                'deadline' => $session->countDeadline?->format('Y-m-d'),
                'deadline_label' => $session->countDeadline?->diffForHumans(),
                'assigned_count' => count($session->counters),
                'counters' => $session->counters,
                'is_overdue' => $session->countDeadline ? $session->countDeadline->isPast() : false,
            ];
        }

        return [
            'active_count' => $counts['counting'] ?? 0,
            'sessions' => $formattedSessions,
            'total_active' => $sessions->total(),
        ];
    }

    // Task Summary
    public function getTaskSummary(): array
    {
        $filters = TaskFilterDTO::fromArray(['per_page' => 1]);
        $allTasks = $this->taskService->getTasks($filters);

        $pendingFilters = TaskFilterDTO::fromArray(['status' => 'pending', 'per_page' => 1]);
        $pendingTasks = $this->taskService->getTasks($pendingFilters);

        $completedFilters = TaskFilterDTO::fromArray(['status' => 'completed', 'per_page' => 1]);
        $completedTasks = $this->taskService->getTasks($completedFilters);

        $inProgressFilters = TaskFilterDTO::fromArray(['status' => 'in_progress', 'per_page' => 1]);
        $inProgressTasks = $this->taskService->getTasks($inProgressFilters);

        $counts = $this->taskService->getStatusCounts();

        // Get overdue tasks
        $overdueTasks = [];
        foreach ($pendingTasks as $task) {
            if ($task->dueDate && $task->dueDate->isPast()) {
                $overdueTasks[] = $task;
            }
        }

        return [
            'total' => $allTasks->total(),
            'pending' => $counts['pending'] ?? 0,
            'in_progress' => $counts['in_progress'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
            'overdue' => $counts['overdue'] ?? 0,
            'recent_overdue' => array_slice($overdueTasks, 0, 3),
        ];
    }

    // Inventory Summary
    public function getInventorySummary(): array
    {
        $filters = InventoryFilterDTO::fromArray(['per_page' => 1]);
        $allInventory = $this->inventoryService->getAll($filters);

        $productFilters = \App\Modules\Product\Application\DTOs\ProductFilterDTO::fromArray(['per_page' => 1]);
        $allProducts = $this->productService->getAll($productFilters);

        // Get low stock items (threshold < 10)
        $lowStockFilters = InventoryFilterDTO::fromArray([
            'low_stock' => true,
            'per_page' => 5,
        ]);
        $lowStockInventory = $this->inventoryService->getAll($lowStockFilters);

        return [
            'total_products' => $allProducts->total(),
            'total_inventory' => $allInventory->total(),
            'low_stock_count' => $lowStockInventory->total(),
            'low_stock_items' => array_slice($lowStockInventory->items(), 0, 5),
            'recent_movements' => $this->getRecentStockMovements(),
        ];
    }

    private function getRecentStockMovements(): array
    {
        // Get recent inventory transactions
        $filters = InventoryFilterDTO::fromArray(['per_page' => 5]);
        $inventory = $this->inventoryService->getAll($filters);

        $movements = [];
        foreach ($inventory as $item) {
            if (isset($item->lastMovementAt)) {
                $movements[] = [
                    'id' => $item->id,
                    'product_name' => $item->productName ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'type' => $item->type ?? 'inventory',
                    'date' => $item->lastMovementAt?->format('M d, H:i'),
                ];
            }
        }

        return $movements;
    }

    // User Activity
    public function getUserActivity(): array
    {
        $users = User::with('roles')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->getRoleNames()->toArray(),
                'last_login' => $u->last_login_at?->diffForHumans(),
                'is_online' => $u->last_login_at && $u->last_login_at->diffInMinutes(now()) < 15,
            ]);

        $operators = $users->filter(fn ($u) => in_array('Operator', $u['roles']));

        return [
            'total_users' => $users->count(),
            'active_operators' => $operators->where('is_online', true)->count(),
            'operators' => $operators->values()->toArray(),
            'recent_logins' => $users->sortByDesc(fn ($u) => $u['last_login'] ?? '1970-01-01')
                ->take(5)
                ->values()
                ->toArray(),
        ];
    }

    // Quick Actions
    public function getQuickActions(): array
    {
        return [
            [
                'id' => 'create_stock_opname',
                'label' => 'Stock Opname',
                'description' => 'Create new stock opname session',
                'icon' => 'clipboard-check',
                'route' => 'stock_opnames.create',
                'color' => 'green',
            ],
            [
                'id' => 'create_task',
                'label' => 'Task',
                'description' => 'Assign new task to operator',
                'icon' => 'assignment',
                'route' => 'tasks.create',
                'color' => 'blue',
            ],
            [
                'id' => 'manage_users',
                'label' => 'Users',
                'description' => 'Manage users and roles',
                'icon' => 'users',
                'route' => 'users.index',
                'color' => 'purple',
            ],
            [
                'id' => 'review_approvals',
                'label' => 'Approvals',
                'description' => 'Review pending approvals',
                'icon' => 'check-circle',
                'route' => 'approvals.queue.all',
                'color' => 'orange',
            ],
        ];
    }

    // Stats for cards
    public function getStats(): array
    {
        return [
            'pending_approvals' => $this->getPendingApprovals()['pending_count'],
            'active_opnames' => $this->getActiveStockOpnames()['active_count'],
            'total_tasks' => $this->getTaskSummary()['total'],
            'overdue_tasks' => $this->getTaskSummary()['overdue'],
            'low_stock_items' => $this->getInventorySummary()['low_stock_count'],
            'active_operators' => $this->getUserActivity()['active_operators'],
        ];
    }
}