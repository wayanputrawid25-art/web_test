<?php

namespace App\Modules\Inventory\Application\Actions;

use App\Modules\Inventory\Application\DTOs\StockInDTO;
use App\Modules\Inventory\Application\DTOs\StockOutDTO;
use App\Modules\Inventory\Application\DTOs\StockAdjustmentDTO;
use App\Modules\Inventory\Domain\Contracts\InventoryRepositoryInterface;
use App\Modules\Inventory\Domain\Entities\InventoryTransaction;
use App\Modules\Inventory\Domain\Entities\StockLedger;
use App\Modules\Inventory\Exceptions\InsufficientStockException;
use App\Modules\Product\Infrastructure\Models\Product;

class InventoryAction
{
    public function __construct(
        private readonly InventoryRepositoryInterface $repository
    ) {}

    public function getTransactions(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->getTransactions($filters, $perPage);
    }

    public function getTransactionById(int $id): ?InventoryTransaction
    {
        return $this->repository->getTransactionById($id);
    }

    public function stockIn(StockInDTO $dto, ?int $userId = null): InventoryTransaction
    {
        $product = Product::find($dto->productId);
        if (!$product) {
            throw new \App\Modules\Inventory\Exceptions\ProductNotFoundException($dto->productId);
        }

        $data = array_merge($dto->toArray(), ['user_id' => $userId]);
        $transaction = $this->repository->createTransaction($data);

        $currentBalance = $this->repository->getStockBalance($dto->productId);
        $newBalance = $currentBalance + $dto->quantity;

        $this->repository->createLedgerEntry([
            'product_id' => $dto->productId,
            'stock_in' => $dto->quantity,
            'stock_out' => 0,
            'balance' => $newBalance,
            'transaction_id' => $transaction->id,
            'transaction_type' => 'stock_in',
            'reference' => $dto->reference,
            'user_id' => $userId,
        ]);

        return $transaction;
    }

    public function stockOut(StockOutDTO $dto, ?int $userId = null): InventoryTransaction
    {
        $product = Product::find($dto->productId);
        if (!$product) {
            throw new \App\Modules\Inventory\Exceptions\ProductNotFoundException($dto->productId);
        }

        $currentBalance = $this->repository->getStockBalance($dto->productId);
        if ($currentBalance < $dto->quantity) {
            throw new InsufficientStockException($dto->productId, $dto->quantity, $currentBalance);
        }

        $data = array_merge($dto->toArray(), ['user_id' => $userId]);
        $transaction = $this->repository->createTransaction($data);

        $newBalance = $currentBalance - $dto->quantity;

        $this->repository->createLedgerEntry([
            'product_id' => $dto->productId,
            'stock_in' => 0,
            'stock_out' => $dto->quantity,
            'balance' => $newBalance,
            'transaction_id' => $transaction->id,
            'transaction_type' => 'stock_out',
            'reference' => $dto->reference,
            'user_id' => $userId,
        ]);

        return $transaction;
    }

    public function adjustStock(StockAdjustmentDTO $dto, ?int $userId = null): InventoryTransaction
    {
        $product = Product::find($dto->productId);
        if (!$product) {
            throw new \App\Modules\Inventory\Exceptions\ProductNotFoundException($dto->productId);
        }

        $currentBalance = $this->repository->getStockBalance($dto->productId);
        $newBalance = $currentBalance + $dto->quantity;

        if ($newBalance < 0) {
            throw new \InvalidArgumentException('Adjustment would result in negative stock');
        }

        $data = array_merge($dto->toArray(), ['user_id' => $userId]);
        $transaction = $this->repository->createTransaction($data);

        $this->repository->createLedgerEntry([
            'product_id' => $dto->productId,
            'stock_in' => $dto->quantity > 0 ? $dto->quantity : 0,
            'stock_out' => $dto->quantity < 0 ? abs($dto->quantity) : 0,
            'balance' => $newBalance,
            'transaction_id' => $transaction->id,
            'transaction_type' => 'adjustment',
            'reference' => $dto->reference,
            'user_id' => $userId,
        ]);

        return $transaction;
    }

    public function getStockBalance(int $productId): int
    {
        return $this->repository->getStockBalance($productId);
    }

    public function getAllStockBalances(): \Illuminate\Support\Collection
    {
        return $this->repository->getStockBalances();
    }

    public function getLedgerByProduct(int $productId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->getLedgerByProduct($productId, $perPage);
    }

    public function getAllLedger(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->getAllLedger($perPage);
    }
}