<?php

namespace App\Modules\TaskCenter\Application\DTOs;

class UpdateTaskDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $priority,
        public readonly int $assigneeId,
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
            'product_id' => $this->productId,
            'inventory_transaction_id' => $this->inventoryTransactionId,
            'due_date' => $this->dueDate,
        ], fn ($value) => $value !== null);
    }
}