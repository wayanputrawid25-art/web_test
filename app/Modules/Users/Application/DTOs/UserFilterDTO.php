<?php

namespace App\Modules\Users\Application\DTOs;

class UserFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $role = null,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            role: $data['role'] ?? null,
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status,
            'role' => $this->role,
        ], fn ($value) => $value !== null);
    }
}