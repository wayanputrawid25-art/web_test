<?php

namespace App\Modules\Inventory\Tests\Unit;

use App\Modules\Inventory\Domain\Entities\InventoryTransaction;
use App\Modules\Inventory\Domain\ValueObjects\TransactionType;
use PHPUnit\Framework\TestCase;

class InventoryTransactionEntityTest extends TestCase
{
    public function test_can_create_inventory_transaction(): void
    {
        $transaction = new InventoryTransaction(
            id: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            type: TransactionType::STOCK_IN,
            quantity: 100,
            reference: 'PO-001',
            notes: 'Initial stock',
            userId: 5
        );

        $this->assertEquals(1, $transaction->id);
        $this->assertEquals(10, $transaction->productId);
        $this->assertEquals('PROD-001', $transaction->productSku);
        $this->assertEquals('Test Product', $transaction->productName);
        $this->assertEquals(TransactionType::STOCK_IN, $transaction->type);
        $this->assertEquals(100, $transaction->quantity);
        $this->assertEquals('PO-001', $transaction->reference);
        $this->assertEquals('Initial stock', $transaction->notes);
        $this->assertEquals(5, $transaction->userId);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $createdAt = new \DateTimeImmutable('2024-01-15 10:30:00');

        $transaction = new InventoryTransaction(
            id: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            type: TransactionType::STOCK_IN,
            quantity: 100,
            reference: 'PO-001',
            notes: 'Initial stock',
            userId: 5,
            createdAt: $createdAt
        );

        $array = $transaction->toArray();

        $this->assertEquals([
            'id' => 1,
            'product_id' => 10,
            'product_sku' => 'PROD-001',
            'product_name' => 'Test Product',
            'type' => 'stock_in',
            'quantity' => 100,
            'reference' => 'PO-001',
            'notes' => 'Initial stock',
            'user_id' => 5,
            'created_at' => '2024-01-15 10:30:00',
        ], $array);
    }

    public function test_to_array_with_null_created_at(): void
    {
        $transaction = new InventoryTransaction(
            id: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            type: TransactionType::STOCK_OUT,
            quantity: 50,
            reference: null,
            notes: null,
            userId: null,
            createdAt: null
        );

        $array = $transaction->toArray();

        $this->assertNull($array['created_at']);
    }
}