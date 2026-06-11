<?php

namespace App\Modules\Approval\Tests\Unit;

use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use PHPUnit\Framework\TestCase;

class ApprovalTypeTest extends TestCase
{
    public function test_all_types_have_correct_values(): void
    {
        $this->assertEquals('stock_opname', ApprovalType::STOCK_OPNAME->value);
        $this->assertEquals('stock_adjustment', ApprovalType::STOCK_ADJUSTMENT->value);
        $this->assertEquals('inventory_correction', ApprovalType::INVENTORY_CORRECTION->value);
        $this->assertEquals('manual_adjustment', ApprovalType::MANUAL_ADJUSTMENT->value);
    }

    public function test_labels_are_correct(): void
    {
        $this->assertEquals('Stock Opname', ApprovalType::STOCK_OPNAME->label());
        $this->assertEquals('Stock Adjustment', ApprovalType::STOCK_ADJUSTMENT->label());
        $this->assertEquals('Inventory Correction', ApprovalType::INVENTORY_CORRECTION->label());
        $this->assertEquals('Manual Adjustment', ApprovalType::MANUAL_ADJUSTMENT->label());
    }

    public function test_icons_are_defined(): void
    {
        $this->assertEquals('clipboard-check', ApprovalType::STOCK_OPNAME->icon());
        $this->assertEquals('adjustments', ApprovalType::STOCK_ADJUSTMENT->icon());
        $this->assertEquals('exclamation-circle', ApprovalType::INVENTORY_CORRECTION->icon());
        $this->assertEquals('pencil-alt', ApprovalType::MANUAL_ADJUSTMENT->icon());
    }
}