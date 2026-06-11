<?php

namespace App\Modules\Product\Tests\Unit;

use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use PHPUnit\Framework\TestCase;

class ProductEntityTest extends TestCase
{
    public function test_can_create_product_entity(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $this->assertEquals(1, $product->id);
        $this->assertEquals('PROD-001', $product->sku);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('Electronics', $product->category);
        $this->assertEquals(ProductStatus::ACTIVE, $product->status);
    }

    public function test_product_to_array(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE,
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-02 15:30:00')
        );

        $array = $product->toArray();

        $this->assertEquals([
            'id' => 1,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'category' => 'Electronics',
            'status' => 'active',
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-02 15:30:00',
        ], $array);
    }

    public function test_product_without_timestamps(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $array = $product->toArray();

        $this->assertNull($array['created_at']);
        $this->assertNull($array['updated_at']);
    }
}