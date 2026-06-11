<?php

namespace App\Modules\StockOpname\Tests\Unit;

use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use PHPUnit\Framework\TestCase;

class StockOpnameItemTest extends TestCase
{
    public function test_can_create_item(): void
    {
        $item = new StockOpnameItem(
            id: 1,
            sessionId: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            systemQuantity: 100.00,
            countedQuantity: 98.00,
            variance: -2.00,
            notes: 'Minor discrepancy',
            counterId: 2,
            counterName: 'Jane Doe',
            countedAt: new \DateTimeImmutable('2024-01-15 10:30:00'),
            createdAt: new \DateTimeImmutable('2024-01-01'),
            updatedAt: new \DateTimeImmutable('2024-01-15'),
        );

        $this->assertEquals(1, $item->id);
        $this->assertEquals(10, $item->productId);
        $this->assertEquals('PROD-001', $item->productSku);
        $this->assertEquals(100.00, $item->systemQuantity);
        $this->assertEquals(98.00, $item->countedQuantity);
        $this->assertEquals(-2.00, $item->variance);
        $this->assertTrue($item->hasVariance());
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $item = new StockOpnameItem(
            id: 1,
            sessionId: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            systemQuantity: 100.00,
            countedQuantity: 100.00,
            variance: 0.00,
            notes: null,
            counterId: 2,
            counterName: 'Jane',
            countedAt: new \DateTimeImmutable('2024-01-15 10:30:00'),
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-15 10:30:00'),
        );

        $array = $item->toArray();

        $this->assertEquals([
            'id' => 1,
            'session_id' => 1,
            'product_id' => 10,
            'product_sku' => 'PROD-001',
            'product_name' => 'Test Product',
            'system_quantity' => 100.00,
            'counted_quantity' => 100.00,
            'variance' => 0.00,
            'notes' => null,
            'counter_id' => 2,
            'counter_name' => 'Jane',
            'counted_at' => '2024-01-15 10:30:00',
        ], $array);
    }

    public function test_has_variance(): void
    {
        $itemWithVariance = new StockOpnameItem(
            id: 1, sessionId: 1, productId: 1, productSku: 'SKU', productName: 'Name',
            systemQuantity: 100, countedQuantity: 98, variance: -2,
            notes: null, counterId: 1, counterName: 'John',
            countedAt: null, createdAt: null, updatedAt: null,
        );

        $itemNoVariance = new StockOpnameItem(
            id: 2, sessionId: 1, productId: 2, productSku: 'SKU2', productName: 'Name2',
            systemQuantity: 100, countedQuantity: 100, variance: 0,
            notes: null, counterId: 1, counterName: 'John',
            countedAt: null, createdAt: null, updatedAt: null,
        );

        $itemNotCounted = new StockOpnameItem(
            id: 3, sessionId: 1, productId: 3, productSku: 'SKU3', productName: 'Name3',
            systemQuantity: 100, countedQuantity: null, variance: null,
            notes: null, counterId: 1, counterName: 'John',
            countedAt: null, createdAt: null, updatedAt: null,
        );

        $this->assertTrue($itemWithVariance->hasVariance());
        $this->assertFalse($itemNoVariance->hasVariance());
        $this->assertFalse($itemNotCounted->hasVariance());
    }

    public function test_calculate_variance(): void
    {
        $item = new StockOpnameItem(
            id: 1, sessionId: 1, productId: 1, productSku: 'SKU', productName: 'Name',
            systemQuantity: 100, countedQuantity: 105, variance: 5,
            notes: null, counterId: 1, counterName: 'John',
            countedAt: null, createdAt: null, updatedAt: null,
        );

        $this->assertEquals(5.00, $item->calculateVariance());
    }

    public function test_calculate_variance_when_not_counted(): void
    {
        $item = new StockOpnameItem(
            id: 1, sessionId: 1, productId: 1, productSku: 'SKU', productName: 'Name',
            systemQuantity: 100, countedQuantity: null, variance: null,
            notes: null, counterId: 1, counterName: 'John',
            countedAt: null, createdAt: null, updatedAt: null,
        );

        $this->assertNull($item->calculateVariance());
    }
}