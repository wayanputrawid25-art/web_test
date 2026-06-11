<?php

namespace App\Modules\TaskCenter\Application\DTOs;

class TaskFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?int $assigneeId = null,
        public readonly ?int $creatorId = null,
        public readonly ?int $productId = null,
        public readonly bool $myTasks = false,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            priority: $data['priority'] ?? null,
            assigneeId: isset($data['assignee_id']) ? (int) $data['assignee_id'] : null,
            creatorId: isset($data['creator_id']) ? (int) $data['creator_id'] : null,
            productId: isset($data['product_id']) ? (int) $data['product_id'] : null,
            myTasks: $data['my_tasks'] ?? false,
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'assignee_id' => $this->assigneeId,
            'creator_id' => $this->creatorId,
            'product_id' => $this->productId,
            'my_tasks' => $this->myTasks,
        ], fn ($value) => $value !== null && $value !== false);
    }
}