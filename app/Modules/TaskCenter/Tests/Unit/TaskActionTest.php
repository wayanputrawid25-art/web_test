<?php

namespace App\Modules\TaskCenter\Tests\Unit;

use App\Modules\TaskCenter\Domain\Contracts\TaskRepositoryInterface;
use App\Modules\TaskCenter\Domain\Entities\Task;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use App\Modules\TaskCenter\Application\Actions\TaskAction;
use App\Modules\TaskCenter\Application\DTOs\CreateTaskDTO;
use App\Modules\TaskCenter\Application\DTOs\ChangeStatusDTO;
use App\Modules\TaskCenter\Exceptions\TaskNotFoundException;
use App\Modules\TaskCenter\Exceptions\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;
use Mockery;

class TaskActionTest extends TestCase
{
    private $repository;
    private TaskAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TaskRepositoryInterface::class);
        $this->action = new TaskAction($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_by_id_returns_task(): void
    {
        $task = $this->createTask();

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($task);

        $result = $this->action->getById(1);

        $this->assertEquals($task, $result);
    }

    public function test_get_by_id_throws_not_found_exception(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->action->getById(999);
    }

    public function test_create_task(): void
    {
        $dto = new CreateTaskDTO(
            title: 'New Task',
            description: 'Description',
            priority: 'medium',
            assigneeId: 1,
            creatorId: 2,
            productId: null,
            inventoryTransactionId: null,
            dueDate: null
        );

        $createdTask = new Task(
            id: 1,
            title: 'New Task',
            description: 'Description',
            status: TaskStatus::DRAFT,
            priority: TaskPriority::MEDIUM,
            assigneeId: 1,
            assigneeName: 'John',
            productId: null,
            productSku: null,
            inventoryTransactionId: null,
            creatorId: 2,
            creatorName: 'Jane',
            dueDate: null,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdTask);

        $this->repository
            ->shouldReceive('createActivityLog')
            ->once()
            ->andReturn(Mockery::mock(\App\Modules\TaskCenter\Domain\Entities\TaskActivityLog::class));

        $result = $this->action->create($dto);

        $this->assertEquals($createdTask, $result);
    }

    public function test_delete_task(): void
    {
        $task = $this->createTask();

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($task);

        $this->repository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->action->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_task_throws_not_found(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(TaskNotFoundException::class);

        $this->action->delete(999);
    }

    public function test_change_status_valid_transition(): void
    {
        $task = $this->createTask(status: TaskStatus::DRAFT);

        $updatedTask = $this->createTask(status: TaskStatus::ASSIGNED);

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($task);

        $this->repository
            ->shouldReceive('update')
            ->with(1, ['status' => 'assigned'])
            ->once()
            ->andReturn($updatedTask);

        $this->repository
            ->shouldReceive('createActivityLog')
            ->once()
            ->andReturn(Mockery::mock(\App\Modules\TaskCenter\Domain\Entities\TaskActivityLog::class));

        $dto = new ChangeStatusDTO('assigned', 'Assigning task');

        $result = $this->action->changeStatus(1, $dto);

        $this->assertEquals(TaskStatus::ASSIGNED, $result->status);
    }

    public function test_change_status_invalid_transition(): void
    {
        $task = $this->createTask(status: TaskStatus::DRAFT);

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($task);

        $dto = new ChangeStatusDTO('in_progress', 'Trying invalid transition');

        $this->expectException(InvalidStatusTransitionException::class);

        $this->action->changeStatus(1, $dto);
    }

    public function test_get_status_counts(): void
    {
        $counts = [
            'draft' => 5,
            'assigned' => 3,
            'in_progress' => 10,
            'review' => 2,
            'approved' => 1,
            'closed' => 8,
        ];

        $this->repository
            ->shouldReceive('countByStatus')
            ->once()
            ->andReturn($counts);

        $result = $this->action->getStatusCounts();

        $this->assertEquals($counts, $result);
    }

    private function createTask(
        int $id = 1,
        TaskStatus $status = TaskStatus::DRAFT,
        TaskPriority $priority = TaskPriority::MEDIUM
    ): Task {
        return new Task(
            id: $id,
            title: 'Test Task',
            description: 'Description',
            status: $status,
            priority: $priority,
            assigneeId: 1,
            assigneeName: 'John',
            productId: null,
            productSku: null,
            inventoryTransactionId: null,
            creatorId: 2,
            creatorName: 'Jane',
            dueDate: null,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );
    }
}