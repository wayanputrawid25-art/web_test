<?php

namespace App\Modules\Inventory\Domain\Entities;

use App\Modules\Inventory\Domain\ValueObjects\TransactionType;
use App\Modules\Inventory\Infrastructure\Models\InventoryTransaction as TransactionModel;

class InventoryTransaction
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productId,
        public readonly string $productSku,
        public readonly string $productName,
        public readonly TransactionType $type,
        public readonly int $quantity,
        public readonly ?string $reference,
        public readonly ?string $notes,
        public readonly ?int $userId,
        public readonly ?\DateTimeImmutable $createdAt = null,
    ) {}

    public static function fromModel(TransactionModel $model): self
    {
        return new self(
            id: $model->id,
            productId: $model->product_id,
            productSku: $model->product?->sku ?? '',
            productName: $model->product?->name ?? '',
            type: TransactionType::from($model->type),
            quantity: $model->quantity,
            reference: $model->reference,
            notes: $model->notes,
            userId: $model->user_id,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'product_sku' => $this->productSku,
            'product_name' => $this->productName,
            'type' => $this->type->value,
            'quantity' => $this->quantity,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}