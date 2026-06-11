<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

enum TransactionType: string
{
    case STOCK_IN = 'stock_in';
    case STOCK_OUT = 'stock_out';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::STOCK_IN => 'Stock In',
            self::STOCK_OUT => 'Stock Out',
            self::ADJUSTMENT => 'Stock Adjustment',
        };
    }

    public function isStockIn(): bool
    {
        return $this === self::STOCK_IN;
    }

    public function isStockOut(): bool
    {
        return $this === self::STOCK_OUT;
    }

    public function isAdjustment(): bool
    {
        return $this === self::ADJUSTMENT;
    }
}