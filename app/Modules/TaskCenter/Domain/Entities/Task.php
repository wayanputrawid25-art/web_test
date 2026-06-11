<?php

namespace App\Modules\TaskCenter\Domain\Entities;

use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use App\Modules\TaskCenter\Infrastructure\Models\Task as TaskModel;

class Task
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly TaskStatus $status,
        public readonly TaskPriority $priority,
        public readonly int $assigneeId,
        public readonly ?string $assigneeName,
        public readonly ?int $productId,
        public readonly ?string $productSku,
        public readonly ?int $inventoryTransactionId,
        public readonly ?int $creatorId,
        public readonly ?string $creatorName,
        public readonly ?\DateTimeImmutable $dueDate,
        public readonly ?\DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
        public readonly array $activityLogs = [],
    ) {}

    public static function fromModel(TaskModel $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            status: TaskStatus::from($model->status),
            priority: TaskPriority::from($model->priority),
            assigneeId: $model->assignee_id,
            assigneeName: $model->assignee?->name,
            productId: $model->product_id,
            productSku: $model->product?->sku,
            inventoryTransactionId: $model->inventory_transaction_id,
            creatorId: $model->creator_id,
            creatorName: $model->creator?->name,
            dueDate: $model->due_date ? \Carbon\Carbon::parse($model->due_date)->toImmutable() : null,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
            activityLogs: [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'assignee_id' => $this->assigneeId,
            'assignee_name' => $this->assigneeName,
            'product_id' => $this->productId,
            'product_sku' => $this->productSku,
            'inventory_transaction_id' => $this->inventoryTransactionId,
            'creator_id' => $this->creatorId,
            'creator_name' => $this->creatorName,
            'due_date' => $this->dueDate?->format('Y-m-d'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function canTransitionTo(TaskStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function getNextStatuses(): array
    {
        return $this->status->getNextStatuses();
    }
}