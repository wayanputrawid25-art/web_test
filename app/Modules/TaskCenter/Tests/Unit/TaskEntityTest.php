<?php

namespace App\Modules\TaskCenter\Tests\Unit;

use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskEntityTest extends TestCase
{
    public function test_can_create_task(): void
    {
        $task = new Task(
            id: 1,
            title: 'Test Task',
            description: 'Task description',
            status: TaskStatus::DRAFT,
            priority: TaskPriority::HIGH,
            assigneeId: 1,
            assigneeName: 'John Doe',
            productId: 10,
            productSku: 'PROD-001',
            inventoryTransactionId: null,
            creatorId: 2,
            creatorName: 'Jane Doe',
            dueDate: new \DateTimeImmutable('2024-12-31'),
            createdAt: new \DateTimeImmutable('2024-01-01'),
            updatedAt: new \DateTimeImmutable('2024-01-02'),
        );

        $this->assertEquals(1, $task->id);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Task description', $task->description);
        $this->assertEquals(TaskStatus::DRAFT, $task->status);
        $this->assertEquals(TaskPriority::HIGH, $task->priority);
        $this->assertEquals(1, $task->assigneeId);
        $this->assertEquals('John Doe', $task->assigneeName);
        $this->assertEquals(10, $task->productId);
        $this->assertEquals('PROD-001', $task->productSku);
        $this->assertNull($task->inventoryTransactionId);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $task = new Task(
            id: 1,
            title: 'Test Task',
            description: 'Description',
            status: TaskStatus::ASSIGNED,
            priority: TaskPriority::MEDIUM,
            assigneeId: 1,
            assigneeName: 'John',
            productId: null,
            productSku: null,
            inventoryTransactionId: null,
            creatorId: 2,
            creatorName: 'Jane',
            dueDate: new \DateTimeImmutable('2024-12-31'),
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-02 11:00:00'),
        );

        $array = $task->toArray();

        $this->assertEquals([
            'id' => 1,
            'title' => 'Test Task',
            'description' => 'Description',
            'status' => 'assigned',
            'priority' => 'medium',
            'assignee_id' => 1,
            'assignee_name' => 'John',
            'product_id' => null,
            'product_sku' => null,
            'inventory_transaction_id' => null,
            'creator_id' => 2,
            'creator_name' => 'Jane',
            'due_date' => '2024-12-31',
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-02 11:00:00',
        ], $array);
    }

    public function test_can_transition_status(): void
    {
        $task = new Task(
            id: 1,
            title: 'Test',
            description: null,
            status: TaskStatus::DRAFT,
            priority: TaskPriority::LOW,
            assigneeId: 1,
            assigneeName: 'John',
            productId: null,
            productSku: null,
            inventoryTransactionId: null,
            creatorId: 1,
            creatorName: 'John',
            dueDate: null,
            createdAt: null,
            updatedAt: null,
        );

        $this->assertTrue($task->canTransitionTo(TaskStatus::ASSIGNED));
        $this->assertFalse($task->canTransitionTo(TaskStatus::IN_PROGRESS));
    }

    public function test_get_next_statuses(): void
    {
        $draftTask = new Task(
            id: 1, title: 'Test', description: null,
            status: TaskStatus::DRAFT,
            priority: TaskPriority::LOW,
            assigneeId: 1, assigneeName: 'John',
            productId: null, productSku: null,
            inventoryTransactionId: null,
            creatorId: 1, creatorName: 'John',
            dueDate: null, createdAt: null, updatedAt: null,
        );

        $this->assertEquals([TaskStatus::ASSIGNED], $draftTask->getNextStatuses());
    }
}