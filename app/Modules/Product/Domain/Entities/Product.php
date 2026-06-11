<?php

namespace App\Modules\Product\Domain\Entities;

use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Infrastructure\Models\Product as ProductModel;

class Product
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $sku,
        public readonly string $name,
        public readonly string $category,
        public readonly ProductStatus $status,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {}

    public static function fromModel(ProductModel $model): self
    {
        return new self(
            id: $model->id,
            sku: $model->sku,
            name: $model->name,
            category: $model->category,
            status: ProductStatus::from($model->status),
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'status' => $this->status->value,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}