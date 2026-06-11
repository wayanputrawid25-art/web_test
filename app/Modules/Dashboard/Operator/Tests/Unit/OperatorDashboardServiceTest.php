<?php

namespace App\Modules\Dashboard\Operator\Tests\Unit;

use App\Modules\Dashboard\Operator\Services\OperatorDashboardService;
use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use PHPUnit\Framework\TestCase;
use Mockery;

class OperatorDashboardServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_service_can_be_instantiated(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        $this->assertInstanceOf(OperatorDashboardService::class, $service);
    }

    public function test_get_quick_stats_returns_expected_structure(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        // Mock the internal calls
        $taskService->shouldReceive('getTasks')
            ->once()
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'total' => 0,
                'items' => collect([]),
            ]));

        $stockOpnameService->shouldReceive('getSessions')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'total' => 0,
                'items' => collect([]),
            ]));

        $stats = $service->getQuickStats(1);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('tasks_today', $stats);
        $this->assertArrayHasKey('tasks_overdue', $stats);
        $this->assertArrayHasKey('active_opname', $stats);
        $this->assertArrayHasKey('pending_approvals', $stats);
    }

    public function test_get_progress_stats_returns_expected_structure(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        $taskService->shouldReceive('getTasks')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'total' => 5,
                'items' => collect([]),
            ]));

        $stockOpnameService->shouldReceive('getSessions')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'total' => 0,
                'items' => collect([]),
            ]));

        $progress = $service->getProgressStats(1);

        $this->assertIsArray($progress);
        $this->assertArrayHasKey('completed_tasks', $progress);
        $this->assertArrayHasKey('pending_tasks', $progress);
        $this->assertArrayHasKey('items_counted', $progress);
        $this->assertArrayHasKey('variance_found', $progress);
        $this->assertArrayHasKey('task_progress_percentage', $progress);
    }

    public function test_get_active_opname_returns_null_when_no_assignments(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        $stockOpnameService->shouldReceive('getSessions')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'total' => 0,
                'first' => null,
                'items' => collect([]),
            ]));

        $activeOpname = $service->getActiveOpname(1);

        $this->assertNull($activeOpname);
    }

    public function test_get_today_tasks_returns_expected_structure(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        $mockTasks = Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class);
        $mockTasks->shouldReceive('filter')->andReturnUsing(function ($callback) {
            return collect([])->filter($callback);
        });
        $mockTasks->shouldReceive('values')->andReturn(collect([]));
        $mockTasks->shouldReceive('total')->andReturn(0);

        $taskService->shouldReceive('getTasks')
            ->once()
            ->andReturn($mockTasks);

        $tasks = $service->getTodayTasks(1);

        $this->assertIsArray($tasks);
        $this->assertArrayHasKey('total', $tasks);
        $this->assertArrayHasKey('upcoming', $tasks);
        $this->assertArrayHasKey('overdue', $tasks);
        $this->assertArrayHasKey('pagination', $tasks);
    }

    public function test_get_recent_activity_returns_array(): void
    {
        $taskService = Mockery::mock(TaskService::class);
        $stockOpnameService = Mockery::mock(StockOpnameService::class);

        $service = new OperatorDashboardService($taskService, $stockOpnameService);

        $taskService->shouldReceive('getTasks')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'items' => collect([]),
            ]));

        $stockOpnameService->shouldReceive('getSessions')
            ->andReturn(Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class, [
                'items' => collect([]),
            ]));

        $activity = $service->getRecentActivity(1, 5);

        $this->assertIsArray($activity);
        $this->assertLessThanOrEqual(5, count($activity));
    }
}