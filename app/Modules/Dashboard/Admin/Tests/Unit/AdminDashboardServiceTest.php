<?php

namespace App\Modules\Dashboard\Admin\Tests\Unit;

use App\Modules\Dashboard\Admin\Services\AdminDashboardService;
use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Product\Application\Services\ProductService;
use PHPUnit\Framework\TestCase;
use Mockery;

class AdminDashboardServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_service_can_be_instantiated(): void
    {
        $approvalService = Mockery::mock(ApprovalService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);
        $taskService = Mockery::mock(TaskService::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $productService = Mockery::mock(ProductService::class);

        $service = new AdminDashboardService(
            $approvalService,
            $stockOpnameService,
            $taskService,
            $inventoryService,
            $productService
        );

        $this->assertInstanceOf(AdminDashboardService::class, $service);
    }

    public function test_get_quick_actions_returns_expected_structure(): void
    {
        $approvalService = Mockery::mock(ApprovalService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);
        $taskService = Mockery::mock(TaskService::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $productService = Mockery::mock(ProductService::class);

        $service = new AdminDashboardService(
            $approvalService,
            $stockOpnameService,
            $taskService,
            $inventoryService,
            $productService
        );

        $actions = $service->getQuickActions();

        $this->assertIsArray($actions);
        $this->assertCount(4, $actions);

        // Check action structure
        $firstAction = $actions[0];
        $this->assertArrayHasKey('id', $firstAction);
        $this->assertArrayHasKey('label', $firstAction);
        $this->assertArrayHasKey('description', $firstAction);
        $this->assertArrayHasKey('icon', $firstAction);
        $this->assertArrayHasKey('route', $firstAction);
        $this->assertArrayHasKey('color', $firstAction);
    }

    public function test_get_pending_approvals_returns_expected_structure(): void
    {
        $approvalService = Mockery::mock(ApprovalService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);
        $taskService = Mockery::mock(TaskService::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $productService = Mockery::mock(ProductService::class);

        $service = new AdminDashboardService(
            $approvalService,
            $stockOpnameService,
            $taskService,
            $inventoryService,
            $productService
        );

        $mockPaginator = Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('items')->andReturn([]);
        $mockPaginator->shouldReceive('total')->andReturn(0);

        $approvalService->shouldReceive('getRequests')->andReturn($mockPaginator);
        $approvalService->shouldReceive('getStatusCounts')->andReturn(['pending' => 0]);

        $result = $service->getPendingApprovals();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pending_count', $result);
        $this->assertArrayHasKey('recent_requests', $result);
        $this->assertArrayHasKey('total_pending', $result);
    }

    public function test_get_task_summary_returns_expected_structure(): void
    {
        $approvalService = Mockery::mock(ApprovalService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);
        $taskService = Mockery::mock(TaskService::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $productService = Mockery::mock(ProductService::class);

        $service = new AdminDashboardService(
            $approvalService,
            $stockOpnameService,
            $taskService,
            $inventoryService,
            $productService
        );

        $mockPaginator = Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('total')->andReturn(0);
        $mockPaginator->shouldReceive('items')->andReturn([]);

        $taskService->shouldReceive('getTasks')->andReturn($mockPaginator);
        $taskService->shouldReceive('getStatusCounts')->andReturn([
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'overdue' => 0,
        ]);

        $result = $service->getTaskSummary();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('pending', $result);
        $this->assertArrayHasKey('in_progress', $result);
        $this->assertArrayHasKey('completed', $result);
        $this->assertArrayHasKey('overdue', $result);
    }

    public function test_get_stats_returns_all_required_keys(): void
    {
        $approvalService = Mockery::mock(ApprovalService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);
        $taskService = Mockery::mock(TaskService::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $productService = Mockery::mock(ProductService::class);

        $service = new AdminDashboardService(
            $approvalService,
            $stockOpnameService,
            $taskService,
            $inventoryService,
            $productService
        );

        // Mock all service calls
        $mockPaginator = Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class);
        $mockPaginator->shouldReceive(['total' => 0, 'items' => collect([])]);
        $mockPaginator->shouldReceive('items')->andReturn([]);

        $approvalService->shouldReceive('getRequests')->andReturn($mockPaginator);
        $approvalService->shouldReceive('getStatusCounts')->andReturn(['pending' => 0]);

        $stockOpnameService->shouldReceive('getSessions')->andReturn($mockPaginator);
        $stockOpnameService->shouldReceive('getStatusCounts')->andReturn(['counting' => 0]);
        $stockOpnameService->shouldReceive('getItems')->andReturn(collect([]));

        $taskService->shouldReceive('getTasks')->andReturn($mockPaginator);
        $taskService->shouldReceive('getStatusCounts')->andReturn([]);

        $inventoryService->shouldReceive('getAll')->andReturn($mockPaginator);
        $productService->shouldReceive('getAll')->andReturn($mockPaginator);

        $stats = $service->getStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('pending_approvals', $stats);
        $this->assertArrayHasKey('active_opnames', $stats);
        $this->assertArrayHasKey('total_tasks', $stats);
        $this->assertArrayHasKey('overdue_tasks', $stats);
        $this->assertArrayHasKey('low_stock_items', $stats);
        $this->assertArrayHasKey('active_operators', $stats);
    }
}