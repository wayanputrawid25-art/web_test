<?php

namespace App\Modules\Product\Application\DTOs;

class CreateProductDTO
{
    public function __construct(
        public readonly string $sku,
        public readonly string $name,
        public readonly string $category,
        public readonly string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'],
            name: $data['name'],
            category: $data['category'],
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'status' => $this->status,
        ];
    }
}