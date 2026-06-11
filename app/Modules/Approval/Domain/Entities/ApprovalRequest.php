<?php

namespace App\Modules\Approval\Domain\Entities;

use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use App\Modules\Approval\Infrastructure\Models\ApprovalRequest as ApprovalRequestModel;

class ApprovalRequest
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $code,
        public readonly ApprovalType $type,
        public readonly ApprovalStatus $status,
        public readonly int $referenceId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly int $requesterId,
        public readonly ?string $requesterName,
        public readonly ?int $approverId,
        public readonly ?string $approverName,
        public readonly ?string $notes,
        public readonly ?\DateTimeImmutable $processedAt,
        public readonly ?\DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
        public readonly array $activityLogs = [],
    ) {}

    public static function fromModel(ApprovalRequestModel $model): self
    {
        return new self(
            id: $model->id,
            code: $model->code,
            type: ApprovalType::from($model->type),
            status: ApprovalStatus::from($model->status),
            referenceId: $model->reference_id,
            title: $model->title,
            description: $model->description,
            requesterId: $model->requester_id,
            requesterName: $model->requester?->name,
            approverId: $model->approver_id,
            approverName: $model->approver?->name,
            notes: $model->notes,
            processedAt: $model->processed_at ? \Carbon\Carbon::parse($model->processed_at)->toImmutable() : null,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'reference_id' => $this->referenceId,
            'title' => $this->title,
            'description' => $this->description,
            'requester_id' => $this->requesterId,
            'requester_name' => $this->requesterName,
            'approver_id' => $this->approverId,
            'approver_name' => $this->approverName,
            'notes' => $this->notes,
            'processed_at' => $this->processedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function canBeProcessedBy(int $userId): bool
    {
        return $this->status->canBeActionedBy($userId, $this->requesterId);
    }
}