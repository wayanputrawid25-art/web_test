<?php

namespace App\Modules\Approval\Domain\Entities;

use App\Modules\Approval\Infrastructure\Models\ApprovalDecision as ApprovalDecisionModel;

class ApprovalDecision
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $requestId,
        public readonly string $decision,
        public readonly int $approverId,
        public readonly ?string $approverName,
        public readonly ?string $comments,
        public readonly ?\DateTimeImmutable $createdAt,
    ) {}

    public static function fromModel(ApprovalDecisionModel $model): self
    {
        return new self(
            id: $model->id,
            requestId: $model->approval_request_id,
            decision: $model->decision,
            approverId: $model->approver_id,
            approverName: $model->approver?->name,
            comments: $model->comments,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->requestId,
            'decision' => $this->decision,
            'approver_id' => $this->approverId,
            'approver_name' => $this->approverName,
            'comments' => $this->comments,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}