<?php

namespace App\Modules\Inventory\Infrastructure\Repositories;

use App\Modules\Inventory\Domain\Contracts\InventoryRepositoryInterface;
use App\Modules\Inventory\Domain\Entities\InventoryTransaction;
use App\Modules\Inventory\Domain\Entities\StockLedger;
use App\Modules\Inventory\Infrastructure\Models\InventoryTransaction as TransactionModel;
use App\Modules\Inventory\Infrastructure\Models\StockLedger as LedgerModel;
use Illuminate\Support\Collection;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function createTransaction(array $data): InventoryTransaction
    {
        $model = TransactionModel::create($data);
        $model->load('product');

        return InventoryTransaction::fromModel($model);
    }

    public function getTransactionById(int $id): ?InventoryTransaction
    {
        $model = TransactionModel::with('product')->find($id);

        return $model ? InventoryTransaction::fromModel($model) : null;
    }

    public function getTransactions(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = TransactionModel::with('product');

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['product_id']) && $filters['product_id']) {
            $query->byProduct($filters['product_id']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function getStockBalance(int $productId): int
    {
        $lastEntry = LedgerModel::where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastEntry?->balance ?? 0;
    }

    public function getStockBalances(): Collection
    {
        return \App\Modules\Product\Infrastructure\Models\Product::with('category')
            ->select('products.*')
            ->selectRaw('COALESCE((SELECT balance FROM stock_ledger WHERE product_id = products.id ORDER BY id DESC LIMIT 1), 0) as current_stock')
            ->get()
            ->map(fn ($product) => [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'current_stock' => (int) $product->current_stock,
            ]);
    }

    public function createLedgerEntry(array $data): StockLedger
    {
        $model = LedgerModel::create($data);

        return StockLedger::fromModel($model);
    }

    public function getLedgerByProduct(int $productId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return LedgerModel::with('product')
            ->byProduct($productId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllLedger(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = LedgerModel::with('product');

        if (request()->has('search') && request('search')) {
            $query->search(request('search'));
        }

        if (request()->has('product_id') && request('product_id')) {
            $query->byProduct(request('product_id'));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}