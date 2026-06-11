<?php

namespace App\Modules\StockOpname\Tests\Unit;

use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use App\Modules\StockOpname\Domain\ValueObjects\TaskPriority;
use PHPUnit\Framework\TestCase;

class StockOpnameSessionTest extends TestCase
{
    public function test_can_create_session(): void
    {
        $session = new StockOpnameSession(
            id: 1,
            code: 'SO-20240101-0001',
            name: 'Monthly Stock Opname',
            description: 'Test description',
            status: StockOpnameStatus::CREATED,
            creatorId: 1,
            creatorName: 'John Doe',
            taskId: null,
            startDate: new \DateTimeImmutable('2024-01-01'),
            endDate: new \DateTimeImmutable('2024-01-31'),
            countDeadline: new \DateTimeImmutable('2024-01-15'),
            createdAt: new \DateTimeImmutable('2024-01-01'),
            updatedAt: new \DateTimeImmutable('2024-01-01'),
            itemCount: 10,
            countedCount: 5,
            varianceCount: 2,
        );

        $this->assertEquals(1, $session->id);
        $this->assertEquals('SO-20240101-0001', $session->code);
        $this->assertEquals('Monthly Stock Opname', $session->name);
        $this->assertEquals(StockOpnameStatus::CREATED, $session->status);
        $this->assertEquals(10, $session->itemCount);
        $this->assertEquals(5, $session->countedCount);
        $this->assertEquals(2, $session->varianceCount);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $session = new StockOpnameSession(
            id: 1,
            code: 'SO-20240101-0001',
            name: 'Test Session',
            description: 'Description',
            status: StockOpnameStatus::ASSIGNED,
            creatorId: 1,
            creatorName: 'John',
            taskId: null,
            startDate: new \DateTimeImmutable('2024-01-01'),
            endDate: new \DateTimeImmutable('2024-01-31'),
            countDeadline: null,
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-01 11:00:00'),
        );

        $array = $session->toArray();

        $this->assertEquals([
            'id' => 1,
            'code' => 'SO-20240101-0001',
            'name' => 'Test Session',
            'description' => 'Description',
            'status' => 'assigned',
            'creator_id' => 1,
            'creator_name' => 'John',
            'task_id' => null,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'count_deadline' => null,
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-01 11:00:00',
        ], $array);
    }

    public function test_can_transition_status(): void
    {
        $session = new StockOpnameSession(
            id: 1,
            code: 'SO-001',
            name: 'Test',
            description: null,
            status: StockOpnameStatus::CREATED,
            creatorId: 1,
            creatorName: 'John',
            taskId: null,
            startDate: null,
            endDate: null,
            countDeadline: null,
            createdAt: null,
            updatedAt: null,
        );

        $this->assertTrue($session->canTransitionTo(StockOpnameStatus::ASSIGNED));
        $this->assertFalse($session->canTransitionTo(StockOpnameStatus::COUNTING));
    }

    public function test_get_progress_percentage(): void
    {
        $session = new StockOpnameSession(
            id: 1, code: 'SO-001', name: 'Test', description: null,
            status: StockOpnameStatus::COUNTING,
            creatorId: 1, creatorName: 'John',
            taskId: null, startDate: null, endDate: null, countDeadline: null,
            createdAt: null, updatedAt: null,
            itemCount: 10,
            countedCount: 7,
        );

        $this->assertEquals(70, $session->getProgressPercentage());
    }

    public function test_get_progress_percentage_with_zero_items(): void
    {
        $session = new StockOpnameSession(
            id: 1, code: 'SO-001', name: 'Test', description: null,
            status: StockOpnameStatus::CREATED,
            creatorId: 1, creatorName: 'John',
            taskId: null, startDate: null, endDate: null, countDeadline: null,
            createdAt: null, updatedAt: null,
            itemCount: 0,
            countedCount: 0,
        );

        $this->assertEquals(0, $session->getProgressPercentage());
    }
}