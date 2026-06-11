<?php

namespace App\Modules\Product\Tests\Unit;

use App\Modules\Product\Domain\ValueObjects\Sku;
use App\Modules\Product\Exceptions\InvalidSkuException;
use PHPUnit\Framework\TestCase;

class SkuTest extends TestCase
{
    public function test_can_create_valid_sku(): void
    {
        $sku = new Sku('PROD-001');

        $this->assertEquals('PROD-001', $sku->value);
        $this->assertEquals('PROD-001', $sku->toString());
    }

    public function test_sku_throws_exception_for_empty_value(): void
    {
        $this->expectException(InvalidSkuException::class);
        $this->expectExceptionMessage('SKU tidak boleh kosong');

        new Sku('');
    }

    public function test_sku_throws_exception_when_too_long(): void
    {
        $this->expectException(InvalidSkuException::class);
        $this->expectExceptionMessage('SKU tidak boleh lebih dari 50 karakter');

        new Sku(str_repeat('A', 51));
    }

    public function test_sku_throws_exception_for_invalid_characters(): void
    {
        $this->expectException(InvalidSkuException::class);
        $this->expectExceptionMessage('SKU hanya boleh mengandung huruf kapital, angka, dan tanda hubung');

        new Sku('prod-001'); // lowercase not allowed
    }

    public function test_sku_throws_exception_for_special_characters(): void
    {
        $this->expectException(InvalidSkuException::class);

        new Sku('PROD@001');
    }

    public function test_two_skus_with_same_value_are_equal(): void
    {
        $sku1 = new Sku('PROD-001');
        $sku2 = new Sku('PROD-001');

        $this->assertTrue($sku1->equals($sku2));
    }

    public function test_two_skus_with_different_values_are_not_equal(): void
    {
        $sku1 = new Sku('PROD-001');
        $sku2 = new Sku('PROD-002');

        $this->assertFalse($sku1->equals($sku2));
    }

    public function test_valid_sku_formats(): void
    {
        $validSkus = ['ABC123', 'PROD-001', 'ITEM-ABC-123', 'A1B2C3'];

        foreach ($validSkus as $skuString) {
            $sku = new Sku($skuString);
            $this->assertEquals($skuString, $sku->value);
        }
    }
}