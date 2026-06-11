<?php

namespace App\Modules\Product\Application\Actions;

use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Domain\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Exceptions\DuplicateSkuException;
use App\Modules\Product\Exceptions\ProductNotFoundException;

class ProductAction
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->findAll($filters);
    }

    public function getById(int $id): Product
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }

    public function create(CreateProductDTO $dto): Product
    {
        $data = $dto->toArray();

        if ($this->repository->existsBySku($data['sku'])) {
            throw new DuplicateSkuException($data['sku']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, UpdateProductDTO $dto): Product
    {
        $data = $dto->toArray();

        if ($this->repository->existsBySku($data['sku'], $id)) {
            throw new DuplicateSkuException($data['sku']);
        }

        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        return $this->repository->delete($id);
    }

    public function getActiveProducts(): \Illuminate\Support\Collection
    {
        return $this->repository->findByStatus(ProductStatus::ACTIVE);
    }

    public function toggleStatus(int $id): Product
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        $newStatus = $product->status->isActive()
            ? ProductStatus::INACTIVE
            : ProductStatus::ACTIVE;

        return $this->repository->update($id, ['status' => $newStatus->value]);
    }
}