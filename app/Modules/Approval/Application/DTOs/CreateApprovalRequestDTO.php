<?php

namespace App\Modules\Approval\Application\DTOs;

class CreateApprovalRequestDTO
{
    public function __construct(
        public readonly string $type,
        public readonly int $referenceId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?int $approverId,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            referenceId: (int) $data['reference_id'],
            title: $data['title'],
            description: $data['description'] ?? null,
            approverId: isset($data['approver_id']) ? (int) $data['approver_id'] : null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'reference_id' => $this->referenceId,
            'title' => $this->title,
            'description' => $this->description,
            'requester_id' => auth()->id(),
            'approver_id' => $this->approverId,
            'notes' => $this->notes,
            'status' => 'pending',
        ], fn ($value) => $value !== null);
    }
}