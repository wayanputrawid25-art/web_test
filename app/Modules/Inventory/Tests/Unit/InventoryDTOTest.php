<?php

namespace App\Modules\Inventory\Tests\Unit;

use App\Modules\Inventory\Application\DTOs\StockInDTO;
use App\Modules\Inventory\Application\DTOs\StockOutDTO;
use App\Modules\Inventory\Application\DTOs\StockAdjustmentDTO;
use App\Modules\Inventory\Application\DTOs\InventoryFilterDTO;
use PHPUnit\Framework\TestCase;

class InventoryDTOTest extends TestCase
{
    public function test_stock_in_dto_from_array(): void
    {
        $data = [
            'product_id' => 1,
            'quantity' => 100,
            'reference' => 'PO-001',
            'notes' => 'Initial stock',
        ];

        $dto = StockInDTO::fromArray($data);

        $this->assertEquals(1, $dto->productId);
        $this->assertEquals(100, $dto->quantity);
        $this->assertEquals('PO-001', $dto->reference);
        $this->assertEquals('Initial stock', $dto->notes);
    }

    public function test_stock_in_dto_to_array(): void
    {
        $dto = new StockInDTO(
            productId: 1,
            quantity: 100,
            reference: 'PO-001',
            notes: 'Initial stock'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'product_id' => 1,
            'type' => 'stock_in',
            'quantity' => 100,
            'reference' => 'PO-001',
            'notes' => 'Initial stock',
        ], $array);
    }

    public function test_stock_out_dto_from_array(): void
    {
        $data = [
            'product_id' => 2,
            'quantity' => 50,
            'reference' => 'SO-001',
        ];

        $dto = StockOutDTO::fromArray($data);

        $this->assertEquals(2, $dto->productId);
        $this->assertEquals(50, $dto->quantity);
        $this->assertEquals('SO-001', $dto->reference);
        $this->assertNull($dto->notes);
    }

    public function test_stock_out_dto_to_array(): void
    {
        $dto = new StockOutDTO(
            productId: 2,
            quantity: 50,
            reference: 'SO-001'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'product_id' => 2,
            'type' => 'stock_out',
            'quantity' => 50,
            'reference' => 'SO-001',
        ], $array);
    }

    public function test_stock_adjustment_dto_from_array(): void
    {
        $data = [
            'product_id' => 3,
            'quantity' => -10,
            'notes' => 'Damaged goods',
        ];

        $dto = StockAdjustmentDTO::fromArray($data);

        $this->assertEquals(3, $dto->productId);
        $this->assertEquals(-10, $dto->quantity);
        $this->assertEquals('Damaged goods', $dto->notes);
    }

    public function test_inventory_filter_dto_defaults(): void
    {
        $dto = InventoryFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->type);
        $this->assertNull($dto->productId);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_inventory_filter_dto_with_values(): void
    {
        $dto = InventoryFilterDTO::fromArray([
            'search' => 'test',
            'type' => 'stock_in',
            'product_id' => 5,
            'per_page' => 25,
        ]);

        $this->assertEquals('test', $dto->search);
        $this->assertEquals('stock_in', $dto->type);
        $this->assertEquals(5, $dto->productId);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_inventory_filter_dto_filters_nulls_in_to_array(): void
    {
        $dto = new InventoryFilterDTO(
            search: null,
            type: 'stock_out',
            productId: null,
            perPage: 20
        );

        $array = $dto->toArray();

        $this->assertArrayNotHasKey('search', $array);
        $this->assertArrayNotHasKey('product_id', $array);
        $this->assertEquals(['type' => 'stock_out'], $array);
    }
}