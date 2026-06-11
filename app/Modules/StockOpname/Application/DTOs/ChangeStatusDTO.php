<?php

namespace App\Modules\StockOpname\Application\DTOs;

class ChangeStatusDTO
{
    public function __construct(
        public readonly string $newStatus,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            newStatus: $data['status'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->newStatus,
            'notes' => $this->notes,
        ];
    }
}