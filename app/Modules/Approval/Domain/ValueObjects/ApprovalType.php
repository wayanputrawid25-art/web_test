<?php

namespace App\Modules\Approval\Domain\ValueObjects;

enum ApprovalType: string
{
    case STOCK_OPNAME = 'stock_opname';
    case STOCK_ADJUSTMENT = 'stock_adjustment';
    case INVENTORY_CORRECTION = 'inventory_correction';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';

    public function label(): string
    {
        return match ($this) {
            self::STOCK_OPNAME => 'Stock Opname',
            self::STOCK_ADJUSTMENT => 'Stock Adjustment',
            self::INVENTORY_CORRECTION => 'Inventory Correction',
            self::MANUAL_ADJUSTMENT => 'Manual Adjustment',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::STOCK_OPNAME => 'Request approval for completed stock opname sessions',
            self::STOCK_ADJUSTMENT => 'Request approval for stock adjustments',
            self::INVENTORY_CORRECTION => 'Request approval for inventory corrections',
            self::MANUAL_ADJUSTMENT => 'Request approval for manual inventory adjustments',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::STOCK_OPNAME => 'clipboard-check',
            self::STOCK_ADJUSTMENT => 'adjustments',
            self::INVENTORY_CORRECTION => 'exclamation-circle',
            self::MANUAL_ADJUSTMENT => 'pencil-alt',
        };
    }
}