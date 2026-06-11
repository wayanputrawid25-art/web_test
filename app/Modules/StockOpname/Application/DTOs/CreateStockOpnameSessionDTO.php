<?php

namespace App\Modules\StockOpname\Application\DTOs;

class CreateStockOpnameSessionDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $taskId,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly ?string $countDeadline,
        public readonly array $productIds = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            taskId: $data['task_id'] ?? null,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            countDeadline: $data['count_deadline'] ?? null,
            productIds: $data['product_ids'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'task_id' => $this->taskId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'count_deadline' => $this->countDeadline,
            'creator_id' => auth()->id(),
            'status' => 'created',
        ], fn ($value) => $value !== null);
    }
}