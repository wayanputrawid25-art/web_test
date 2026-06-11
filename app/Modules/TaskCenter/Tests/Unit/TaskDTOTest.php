<?php

namespace App\Modules\TaskCenter\Tests\Unit;

use App\Modules\TaskCenter\Application\DTOs\CreateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\UpdateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\TaskFilterDTO;
use App\Modules\TaskCenter\Application\DTOs\ChangeStatusDTO;
use PHPUnit\Framework\TestCase;

class TaskDTOTest extends TestCase
{
    public function test_create_task_dto_from_array(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Task description',
            'priority' => 'high',
            'assignee_id' => 1,
            'creator_id' => 2,
            'product_id' => 10,
            'inventory_transaction_id' => null,
            'due_date' => '2024-12-31',
        ];

        $dto = CreateTaskDTO::fromArray($data);

        $this->assertEquals('Test Task', $dto->title);
        $this->assertEquals('Task description', $dto->description);
        $this->assertEquals('high', $dto->priority);
        $this->assertEquals(1, $dto->assigneeId);
        $this->assertEquals(2, $dto->creatorId);
        $this->assertEquals(10, $dto->productId);
        $this->assertEquals('2024-12-31', $dto->dueDate);
    }

    public function test_create_task_dto_to_array(): void
    {
        $dto = new CreateTaskDTO(
            title: 'Test',
            description: 'Desc',
            priority: 'medium',
            assigneeId: 1,
            creatorId: 2,
            productId: null,
            inventoryTransactionId: null,
            dueDate: '2024-12-31'
        );

        $array = $dto->toArray();

        $this->assertEquals('Test', $array['title']);
        $this->assertEquals('Desc', $array['description']);
        $this->assertEquals('medium', $array['priority']);
        $this->assertEquals(1, $array['assignee_id']);
        $this->assertEquals(2, $array['creator_id']);
        $this->assertEquals('draft', $array['status']);
        $this->assertArrayNotHasKey('product_id', $array);
    }

    public function test_update_task_dto_from_array(): void
    {
        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'priority' => 'urgent',
            'assignee_id' => 3,
            'product_id' => 20,
            'due_date' => '2025-01-15',
        ];

        $dto = UpdateTaskDTO::fromArray($data);

        $this->assertEquals('Updated Task', $dto->title);
        $this->assertEquals('urgent', $dto->priority);
        $this->assertEquals(3, $dto->assigneeId);
    }

    public function test_task_filter_dto_defaults(): void
    {
        $dto = TaskFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->status);
        $this->assertNull($dto->priority);
        $this->assertFalse($dto->myTasks);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_task_filter_dto_with_values(): void
    {
        $dto = TaskFilterDTO::fromArray([
            'search' => 'test',
            'status' => 'in_progress',
            'priority' => 'high',
            'assignee_id' => 5,
            'my_tasks' => true,
            'per_page' => 25,
        ]);

        $this->assertEquals('test', $dto->search);
        $this->assertEquals('in_progress', $dto->status);
        $this->assertEquals('high', $dto->priority);
        $this->assertEquals(5, $dto->assigneeId);
        $this->assertTrue($dto->myTasks);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_change_status_dto(): void
    {
        $dto = ChangeStatusDTO::fromArray([
            'status' => 'in_progress',
            'notes' => 'Starting work',
        ]);

        $this->assertEquals('in_progress', $dto->newStatus);
        $this->assertEquals('Starting work', $dto->notes);
    }
}