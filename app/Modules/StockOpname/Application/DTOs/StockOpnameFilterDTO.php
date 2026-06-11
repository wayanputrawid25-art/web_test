<?php

namespace App\Modules\StockOpname\Application\DTOs;

class StockOpnameFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?int $creatorId = null,
        public readonly bool $myAssignments = false,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            creatorId: isset($data['creator_id']) ? (int) $data['creator_id'] : null,
            myAssignments: $data['my_assignments'] ?? false,
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status,
            'creator_id' => $this->creatorId,
            'my_assignments' => $this->myAssignments ?: null,
        ], fn ($value) => $value !== null && $value !== false);
    }
}