<?php

namespace App\Modules\Inventory\Application\DTOs;

class StockOutDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly ?string $reference = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: $data['quantity'],
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'product_id' => $this->productId,
            'type' => 'stock_out',
            'quantity' => $this->quantity,
            'reference' => $this->reference,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }
}