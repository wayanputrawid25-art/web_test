<?php

namespace App\Modules\StockOpname\Domain\Entities;

use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use App\Modules\StockOpname\Infrastructure\Models\StockOpnameSession as StockOpnameSessionModel;

class StockOpnameSession
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $description,
        public readonly StockOpnameStatus $status,
        public readonly int $creatorId,
        public readonly ?string $creatorName,
        public readonly ?int $taskId,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly ?\DateTimeImmutable $countDeadline,
        public readonly ?\DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
        public readonly int $itemCount = 0,
        public readonly int $countedCount = 0,
        public readonly int $varianceCount = 0,
        public readonly array $counters = [],
        public readonly array $items = [],
        public readonly array $activityLogs = [],
    ) {}

    public static function fromModel(StockOpnameSessionModel $model): self
    {
        return new self(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            description: $model->description,
            status: StockOpnameStatus::from($model->status),
            creatorId: $model->creator_id,
            creatorName: $model->creator?->name,
            taskId: $model->task_id,
            startDate: $model->start_date ? \Carbon\Carbon::parse($model->start_date)->toImmutable() : null,
            endDate: $model->end_date ? \Carbon\Carbon::parse($model->end_date)->toImmutable() : null,
            countDeadline: $model->count_deadline ? \Carbon\Carbon::parse($model->count_deadline)->toImmutable() : null,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
            itemCount: $model->items->count(),
            countedCount: $model->items->whereNotNull('counted_quantity')->count(),
            varianceCount: $model->items->whereNotNull('variance')->where('variance', '!=', 0)->count(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
            'creator_id' => $this->creatorId,
            'creator_name' => $this->creatorName,
            'task_id' => $this->taskId,
            'start_date' => $this->startDate?->format('Y-m-d'),
            'end_date' => $this->endDate?->format('Y-m-d'),
            'count_deadline' => $this->countDeadline?->format('Y-m-d'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function canTransitionTo(StockOpnameStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function getNextStatuses(): array
    {
        return $this->status->getNextStatuses();
    }

    public function getProgressPercentage(): int
    {
        if ($this->itemCount === 0) {
            return 0;
        }

        return (int) round(($this->countedCount / $this->itemCount) * 100);
    }
}