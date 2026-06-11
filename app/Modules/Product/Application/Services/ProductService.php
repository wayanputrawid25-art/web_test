<?php

namespace App\Modules\Product\Application\Services;

use App\Modules\Product\Application\Actions\ProductAction;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\ProductFilterDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Exceptions\DuplicateSkuException;
use App\Modules\Product\Exceptions\ProductNotFoundException;

class ProductService
{
    public function __construct(
        private readonly ProductAction $action
    ) {}

    public function getProducts(ProductFilterDTO $filter): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->action->getAll($filter->toArray());
    }

    public function getProduct(int $id): Product
    {
        return $this->action->getById($id);
    }

    public function createProduct(array $data): Product
    {
        $dto = CreateProductDTO::fromArray($data);

        return $this->action->create($dto);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $dto = UpdateProductDTO::fromArray($data);

        return $this->action->update($id, $dto);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->action->delete($id);
    }

    public function getActiveProducts(): \Illuminate\Support\Collection
    {
        return $this->action->getActiveProducts();
    }

    public function toggleProductStatus(int $id): Product
    {
        return $this->action->toggleStatus($id);
    }

    public function checkSkuAvailability(string $sku, ?int $excludeId = null): bool
    {
        $repository = $this->action->repository;

        return !$repository->existsBySku($sku, $excludeId);
    }
}