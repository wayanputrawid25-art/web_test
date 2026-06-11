<?php

namespace App\Modules\Product\Domain\Contracts;

use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    
    public function findBySku(string $sku): ?Product;
    
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    public function create(array $data): Product;
    
    public function update(int $id, array $data): Product;
    
    public function delete(int $id): bool;
    
    public function existsBySku(string $sku, ?int $excludeId = null): bool;
    
    public function findByStatus(ProductStatus $status): Collection;
}