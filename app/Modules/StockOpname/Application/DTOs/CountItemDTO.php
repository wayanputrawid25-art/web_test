<?php

namespace App\Modules\StockOpname\Application\DTOs;

class CountItemDTO
{
    public function __construct(
        public readonly float $countedQuantity,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            countedQuantity: (float) $data['counted_quantity'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'counted_quantity' => $this->countedQuantity,
            'notes' => $this->notes,
            'counter_id' => auth()->id(),
            'counted_at' => now(),
        ];
    }
}