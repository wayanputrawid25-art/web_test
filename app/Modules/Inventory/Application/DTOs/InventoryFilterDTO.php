<?php

namespace App\Modules\Inventory\Application\DTOs;

class InventoryFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $type = null,
        public readonly ?int $productId = null,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            type: $data['type'] ?? null,
            productId: isset($data['product_id']) ? (int) $data['product_id'] : null,
            perPage: (int) ($data['per_page'] ?? 15),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'type' => $this->type,
            'product_id' => $this->productId,
        ], fn ($value) => $value !== null);
    }
}