<?php

namespace App\Modules\TaskCenter\Application\DTOs;

class CreateTaskDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $priority,
        public readonly int $assigneeId,
        public readonly int $creatorId,
        public readonly ?int $productId,
        public readonly ?int $inventoryTransactionId,
        public readonly ?string $dueDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            priority: $data['priority'] ?? 'medium',
            assigneeId: $data['assignee_id'],
            creatorId: $data['creator_id'],
            productId: $data['product_id'] ?? null,
            inventoryTransactionId: $data['inventory_transaction_id'] ?? null,
            dueDate: $data['due_date'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'assignee_id' => $this->assigneeId,
            'creator_id' => $this->creatorId,
            'product_id' => $this->productId,
            'inventory_transaction_id' => $this->inventoryTransactionId,
            'due_date' => $this->dueDate,
            'status' => 'draft',
        ], fn ($value) => $value !== null);
    }
}