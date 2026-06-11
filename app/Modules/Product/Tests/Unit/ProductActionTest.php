<?php

namespace App\Modules\Product\Tests\Unit;

use App\Modules\Product\Domain\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Application\Actions\ProductAction;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Exceptions\DuplicateSkuException;
use App\Modules\Product\Exceptions\ProductNotFoundException;
use PHPUnit\Framework\TestCase;
use Mockery;

class ProductActionTest extends TestCase
{
    private $repository;
    private ProductAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ProductRepositoryInterface::class);
        $this->action = new ProductAction($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_paginated_products(): void
    {
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        $this->repository
            ->shouldReceive('findAll')
            ->with(['search' => 'test'], 15)
            ->once()
            ->andReturn($paginator);

        $result = $this->action->getAll(['search' => 'test']);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
    }

    public function test_get_by_id_returns_product(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($product);

        $result = $this->action->getById(1);

        $this->assertEquals($product, $result);
    }

    public function test_get_by_id_throws_not_found_exception(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $this->action->getById(999);
    }

    public function test_create_product_successfully(): void
    {
        $dto = new CreateProductDTO(
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: 'active'
        );

        $createdProduct = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('existsBySku')
            ->with('PROD-001')
            ->once()
            ->andReturn(false);

        $this->repository
            ->shouldReceive('create')
            ->with($dto->toArray())
            ->once()
            ->andReturn($createdProduct);

        $result = $this->action->create($dto);

        $this->assertEquals($createdProduct, $result);
    }

    public function test_create_product_throws_duplicate_sku_exception(): void
    {
        $dto = new CreateProductDTO(
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics'
        );

        $this->repository
            ->shouldReceive('existsBySku')
            ->with('PROD-001')
            ->once()
            ->andReturn(true);

        $this->expectException(DuplicateSkuException::class);

        $this->action->create($dto);
    }

    public function test_update_product_successfully(): void
    {
        $dto = new UpdateProductDTO(
            sku: 'PROD-002',
            name: 'Updated Product',
            category: 'Clothing',
            status: 'inactive'
        );

        $existingProduct = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $updatedProduct = new Product(
            id: 1,
            sku: 'PROD-002',
            name: 'Updated Product',
            category: 'Clothing',
            status: ProductStatus::INACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($existingProduct);

        $this->repository
            ->shouldReceive('existsBySku')
            ->with('PROD-002', 1)
            ->once()
            ->andReturn(false);

        $this->repository
            ->shouldReceive('update')
            ->with(1, $dto->toArray())
            ->once()
            ->andReturn($updatedProduct);

        $result = $this->action->update(1, $dto);

        $this->assertEquals($updatedProduct, $result);
    }

    public function test_update_product_throws_not_found_exception(): void
    {
        $dto = new UpdateProductDTO(
            sku: 'PROD-002',
            name: 'Updated Product',
            category: 'Clothing',
            status: 'inactive'
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $this->action->update(999, $dto);
    }

    public function test_delete_product_successfully(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($product);

        $this->repository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->action->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_product_throws_not_found_exception(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(ProductNotFoundException::class);

        $this->action->delete(999);
    }

    public function test_toggle_status(): void
    {
        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::ACTIVE
        );

        $toggledProduct = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Test Product',
            category: 'Electronics',
            status: ProductStatus::INACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($product);

        $this->repository
            ->shouldReceive('update')
            ->with(1, ['status' => 'inactive'])
            ->once()
            ->andReturn($toggledProduct);

        $result = $this->action->toggleStatus(1);

        $this->assertEquals(ProductStatus::INACTIVE, $result->status);
    }
}