<?php

namespace App\Modules\Approval\Application\DTOs;

class ApprovalDecisionDTO
{
    public function __construct(
        public readonly string $decision,
        public readonly ?string $comments,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            decision: $data['decision'],
            comments: $data['comments'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'decision' => $this->decision,
            'comments' => $this->comments,
        ];
    }
}