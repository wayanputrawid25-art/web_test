<?php

namespace App\Modules\StockOpname\Domain\Entities;

use App\Modules\StockOpname\Infrastructure\Models\StockOpnameAssignment as StockOpnameAssignmentModel;

class StockOpnameAssignment
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $sessionId,
        public readonly int $userId,
        public readonly ?string $userName,
        public readonly ?string $userEmail,
        public readonly int $assignedBy,
        public readonly ?string $assignedByName,
        public readonly ?\DateTimeImmutable $assignedAt,
        public readonly ?\DateTimeImmutable $createdAt,
    ) {}

    public static function fromModel(StockOpnameAssignmentModel $model): self
    {
        return new self(
            id: $model->id,
            sessionId: $model->stock_opname_session_id,
            userId: $model->user_id,
            userName: $model->user?->name,
            userEmail: $model->user?->email,
            assignedBy: $model->assigned_by,
            assignedByName: $model->assignedBy?->name,
            assignedAt: $model->assigned_at ? \Carbon\Carbon::parse($model->assigned_at)->toImmutable() : null,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->sessionId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'assigned_by' => $this->assignedBy,
            'assigned_by_name' => $this->assignedByName,
            'assigned_at' => $this->assignedAt?->format('Y-m-d H:i:s'),
        ];
    }
}