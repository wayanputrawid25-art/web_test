<?php

namespace App\Modules\TaskCenter\Domain\Entities;

class TaskActivityLog
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $taskId,
        public readonly string $action,
        public readonly ?string $oldValue,
        public readonly ?string $newValue,
        public readonly int $userId,
        public readonly ?string $userName,
        public readonly ?string $notes,
        public readonly ?\DateTimeImmutable $createdAt,
    ) {}

    public static function fromModel(\App\Modules\TaskCenter\Infrastructure\Models\TaskActivityLog $model): self
    {
        return new self(
            id: $model->id,
            taskId: $model->task_id,
            action: $model->action,
            oldValue: $model->old_value,
            newValue: $model->new_value,
            userId: $model->user_id,
            userName: $model->user?->name,
            notes: $model->notes,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->taskId,
            'action' => $this->action,
            'old_value' => $this->oldValue,
            'new_value' => $this->newValue,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}