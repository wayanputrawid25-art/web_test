<?php

namespace App\Modules\Product\Tests\Unit;

use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Application\DTOs\ProductFilterDTO;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function test_create_product_dto_from_array(): void
    {
        $data = [
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'category' => 'Electronics',
            'status' => 'active',
        ];

        $dto = CreateProductDTO::fromArray($data);

        $this->assertEquals('PROD-001', $dto->sku);
        $this->assertEquals('Test Product', $dto->name);
        $this->assertEquals('Electronics', $dto->category);
        $this->assertEquals('active', $dto->status);
    }

    public function test_create_product_dto_defaults_status_to_active(): void
    {
        $data = [
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'category' => 'Electronics',
        ];

        $dto = CreateProductDTO::fromArray($data);

        $this->assertEquals('active', $dto->status);
    }

    public function test_create_product_dto_to_array(): void
    {
        $dto = new CreateProductDTO(
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: 'active'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'category' => 'Electronics',
            'status' => 'active',
        ], $array);
    }

    public function test_update_product_dto_from_array(): void
    {
        $data = [
            'sku' => 'PROD-002',
            'name' => 'Updated Product',
            'category' => 'Clothing',
            'status' => 'inactive',
        ];

        $dto = UpdateProductDTO::fromArray($data);

        $this->assertEquals('PROD-002', $dto->sku);
        $this->assertEquals('Updated Product', $dto->name);
        $this->assertEquals('Clothing', $dto->category);
        $this->assertEquals('inactive', $dto->status);
    }

    public function test_update_product_dto_to_array(): void
    {
        $dto = new UpdateProductDTO(
            sku: 'PROD-002',
            name: 'Updated Product',
            category: 'Clothing',
            status: 'inactive'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'sku' => 'PROD-002',
            'name' => 'Updated Product',
            'category' => 'Clothing',
            'status' => 'inactive',
        ], $array);
    }

    public function test_product_filter_dto_from_array(): void
    {
        $data = [
            'search' => 'test',
            'status' => 'active',
            'category' => 'Electronics',
            'per_page' => 25,
        ];

        $dto = ProductFilterDTO::fromArray($data);

        $this->assertEquals('test', $dto->search);
        $this->assertEquals('active', $dto->status);
        $this->assertEquals('Electronics', $dto->category);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_product_filter_dto_defaults(): void
    {
        $dto = ProductFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->status);
        $this->assertNull($dto->category);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_product_filter_dto_filters_null_values(): void
    {
        $dto = new ProductFilterDTO(
            search: null,
            status: 'active',
            category: null,
            perPage: 20
        );

        $array = $dto->toArray();

        $this->assertArrayNotHasKey('search', $array);
        $this->assertEquals(['status' => 'active'], $array);
    }
}