<?php

namespace App\Modules\StockOpname\Domain\Entities;

use App\Modules\StockOpname\Infrastructure\Models\StockOpnameItem as StockOpnameItemModel;

class StockOpnameItem
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $sessionId,
        public readonly int $productId,
        public readonly ?string $productSku,
        public readonly ?string $productName,
        public readonly float $systemQuantity,
        public readonly ?float $countedQuantity,
        public readonly ?float $variance,
        public readonly ?string $notes,
        public readonly int $counterId,
        public readonly ?string $counterName,
        public readonly ?\DateTimeImmutable $countedAt,
        public readonly ?\DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {}

    public static function fromModel(StockOpnameItemModel $model): self
    {
        return new self(
            id: $model->id,
            sessionId: $model->stock_opname_session_id,
            productId: $model->product_id,
            productSku: $model->product?->sku,
            productName: $model->product?->name,
            systemQuantity: $model->system_quantity,
            countedQuantity: $model->counted_quantity,
            variance: $model->variance,
            notes: $model->notes,
            counterId: $model->counter_id,
            counterName: $model->counter?->name,
            countedAt: $model->counted_at ? \Carbon\Carbon::parse($model->counted_at)->toImmutable() : null,
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->sessionId,
            'product_id' => $this->productId,
            'product_sku' => $this->productSku,
            'product_name' => $this->productName,
            'system_quantity' => $this->systemQuantity,
            'counted_quantity' => $this->countedQuantity,
            'variance' => $this->variance,
            'notes' => $this->notes,
            'counter_id' => $this->counterId,
            'counter_name' => $this->counterName,
            'counted_at' => $this->countedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function hasVariance(): bool
    {
        return $this->variance !== null && $this->variance != 0;
    }

    public function calculateVariance(): ?float
    {
        if ($this->countedQuantity === null) {
            return null;
        }

        return round($this->countedQuantity - $this->systemQuantity, 2);
    }
}