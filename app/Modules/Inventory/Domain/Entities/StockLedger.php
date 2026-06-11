<?php

namespace App\Modules\Inventory\Domain\Entities;

use App\Modules\Inventory\Infrastructure\Models\StockLedger as LedgerModel;

class StockLedger
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productId,
        public readonly string $productSku,
        public readonly string $productName,
        public readonly int $stockIn,
        public readonly int $stockOut,
        public readonly int $balance,
        public readonly int $transactionId,
        public readonly string $transactionType,
        public readonly ?string $reference,
        public readonly ?int $userId,
        public readonly ?\DateTimeImmutable $createdAt = null,
    ) {}

    public static function fromModel(LedgerModel $model): self
    {
        return new self(
            id: $model->id,
            productId: $model->product_id,
            productSku: $model->product?->sku ?? '',
            productName: $model->product?->name ?? '',
            stockIn: $model->stock_in,
            stockOut: $model->stock_out,
            balance: $model->balance,
            transactionId: $model->transaction_id,
            transactionType: $model->transaction_type,
            reference: $model->reference,
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
            'stock_in' => $this->stockIn,
            'stock_out' => $this->stockOut,
            'balance' => $this->balance,
            'transaction_id' => $this->transactionId,
            'transaction_type' => $this->transactionType,
            'reference' => $this->reference,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}