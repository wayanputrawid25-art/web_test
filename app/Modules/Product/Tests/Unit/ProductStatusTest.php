<?php

namespace App\Modules\Product\Tests\Unit;

use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use PHPUnit\Framework\TestCase;

class ProductStatusTest extends TestCase
{
    public function test_active_status_returns_correct_label(): void
    {
        $status = ProductStatus::ACTIVE;

        $this->assertEquals('Active', $status->label());
        $this->assertTrue($status->isActive());
    }

    public function test_inactive_status_returns_correct_label(): void
    {
        $status = ProductStatus::INACTIVE;

        $this->assertEquals('Inactive', $status->label());
        $this->assertFalse($status->isActive());
    }

    public function test_status_value_matches_expected(): void
    {
        $this->assertEquals('active', ProductStatus::ACTIVE->value);
        $this->assertEquals('inactive', ProductStatus::INACTIVE->value);
    }
}