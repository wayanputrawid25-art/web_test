<?php

namespace App\Modules\Inventory\Tests\Unit;

use App\Modules\Inventory\Domain\ValueObjects\TransactionType;
use PHPUnit\Framework\TestCase;

class TransactionTypeTest extends TestCase
{
    public function test_stock_in_has_correct_value(): void
    {
        $type = TransactionType::STOCK_IN;

        $this->assertEquals('stock_in', $type->value);
        $this->assertEquals('Stock In', $type->label());
        $this->assertTrue($type->isStockIn());
        $this->assertFalse($type->isStockOut());
        $this->assertFalse($type->isAdjustment());
    }

    public function test_stock_out_has_correct_value(): void
    {
        $type = TransactionType::STOCK_OUT;

        $this->assertEquals('stock_out', $type->value);
        $this->assertEquals('Stock Out', $type->label());
        $this->assertFalse($type->isStockIn());
        $this->assertTrue($type->isStockOut());
        $this->assertFalse($type->isAdjustment());
    }

    public function test_adjustment_has_correct_value(): void
    {
        $type = TransactionType::ADJUSTMENT;

        $this->assertEquals('adjustment', $type->value);
        $this->assertEquals('Stock Adjustment', $type->label());
        $this->assertFalse($type->isStockIn());
        $this->assertFalse($type->isStockOut());
        $this->assertTrue($type->isAdjustment());
    }

    public function test_can_create_from_string(): void
    {
        $type = TransactionType::from('stock_in');

        $this->assertEquals(TransactionType::STOCK_IN, $type);
    }

    public function test_all_cases_exist(): void
    {
        $cases = TransactionType::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(TransactionType::STOCK_IN, $cases);
        $this->assertContains(TransactionType::STOCK_OUT, $cases);
        $this->assertContains(TransactionType::ADJUSTMENT, $cases);
    }
}