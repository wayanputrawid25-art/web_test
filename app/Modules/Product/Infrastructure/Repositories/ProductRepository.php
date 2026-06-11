<?php

namespace App\Modules\Product\Infrastructure\Repositories;

use App\Modules\Product\Domain\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Domain\Entities\Product;
use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use App\Modules\Product\Infrastructure\Models\Product as ProductModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        $model = ProductModel::find($id);

        return $model ? Product::fromModel($model) : null;
    }

    public function findBySku(string $sku): ?Product
    {
        $model = ProductModel::where('sku', $sku)->first();

        return $model ? Product::fromModel($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProductModel::query();

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category']) && $filters['category']) {
            $query->byCategory($filters['category']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function create(array $data): Product
    {
        $model = ProductModel::create($data);

        return Product::fromModel($model);
    }

    public function update(int $id, array $data): Product
    {
        $model = ProductModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return Product::fromModel($model);
    }

    public function delete(int $id): bool
    {
        $model = ProductModel::findOrFail($id);

        return $model->delete();
    }

    public function existsBySku(string $sku, ?int $excludeId = null): bool
    {
        $query = ProductModel::where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function findByStatus(ProductStatus $status): Collection
    {
        return ProductModel::where('status', $status->value)
            ->get()
            ->map(fn ($model) => Product::fromModel($model));
    }
}