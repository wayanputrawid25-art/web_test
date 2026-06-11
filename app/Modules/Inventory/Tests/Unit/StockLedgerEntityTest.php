<?php

namespace App\Modules\Inventory\Tests\Unit;

use App\Modules\Inventory\Domain\Entities\StockLedger;
use PHPUnit\Framework\TestCase;

class StockLedgerEntityTest extends TestCase
{
    public function test_can_create_stock_ledger(): void
    {
        $ledger = new StockLedger(
            id: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            stockIn: 100,
            stockOut: 0,
            balance: 100,
            transactionId: 5,
            transactionType: 'stock_in',
            reference: 'PO-001',
            userId: 1
        );

        $this->assertEquals(1, $ledger->id);
        $this->assertEquals(10, $ledger->productId);
        $this->assertEquals('PROD-001', $ledger->productSku);
        $this->assertEquals('Test Product', $ledger->productName);
        $this->assertEquals(100, $ledger->stockIn);
        $this->assertEquals(0, $ledger->stockOut);
        $this->assertEquals(100, $ledger->balance);
        $this->assertEquals(5, $ledger->transactionId);
        $this->assertEquals('stock_in', $ledger->transactionType);
        $this->assertEquals('PO-001', $ledger->reference);
        $this->assertEquals(1, $ledger->userId);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $createdAt = new \DateTimeImmutable('2024-01-15 10:30:00');

        $ledger = new StockLedger(
            id: 1,
            productId: 10,
            productSku: 'PROD-001',
            productName: 'Test Product',
            stockIn: 50,
            stockOut: 20,
            balance: 130,
            transactionId: 5,
            transactionType: 'adjustment',
            reference: 'ADJ-001',
            userId: 1,
            createdAt: $createdAt
        );

        $array = $ledger->toArray();

        $this->assertEquals([
            'id' => 1,
            'product_id' => 10,
            'product_sku' => 'PROD-001',
            'product_name' => 'Test Product',
            'stock_in' => 50,
            'stock_out' => 20,
            'balance' => 130,
            'transaction_id' => 5,
            'transaction_type' => 'adjustment',
            'reference' => 'ADJ-001',
            'user_id' => 1,
            'created_at' => '2024-01-15 10:30:00',
        ], $array);
    }
}