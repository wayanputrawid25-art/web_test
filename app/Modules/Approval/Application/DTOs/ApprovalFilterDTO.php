<?php

namespace App\Modules\Approval\Application\DTOs;

class ApprovalFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $type = null,
        public readonly ?string $status = null,
        public readonly bool $myRequests = false,
        public readonly bool $pendingForMe = false,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            type: $data['type'] ?? null,
            status: $data['status'] ?? null,
            myRequests: $data['my_requests'] ?? false,
            pendingForMe: $data['pending_for_me'] ?? false,
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'type' => $this->type,
            'status' => $this->status,
            'my_requests' => $this->myRequests ?: null,
            'pending_for_me' => $this->pendingForMe ?: null,
        ], fn ($value) => $value !== null && $value !== false);
    }
}