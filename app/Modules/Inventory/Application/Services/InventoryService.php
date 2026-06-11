<?php

namespace App\Modules\Inventory\Application\Services;

use App\Modules\Inventory\Application\Actions\InventoryAction;
use App\Modules\Inventory\Application\DTOs\InventoryFilterDTO;
use App\Modules\Inventory\Application\DTOs\StockAdjustmentDTO;
use App\Modules\Inventory\Application\DTOs\StockInDTO;
use App\Modules\Inventory\Application\DTOs\StockOutDTO;
use App\Modules\Inventory\Domain\Entities\InventoryTransaction;
use App\Modules\Inventory\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Exceptions\ProductNotFoundException;

class InventoryService
{
    public function __construct(
        private readonly InventoryAction $action
    ) {}

    public function getTransactions(InventoryFilterDTO $filter): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->action->getTransactions($filter->toArray(), $filter->perPage);
    }

    public function getTransaction(int $id): ?InventoryTransaction
    {
        return $this->action->getTransactionById($id);
    }

    public function stockIn(array $data, ?int $userId = null): InventoryTransaction
    {
        $dto = StockInDTO::fromArray($data);

        return $this->action->stockIn($dto, $userId);
    }

    public function stockOut(array $data, ?int $userId = null): InventoryTransaction
    {
        $dto = StockOutDTO::fromArray($data);

        return $this->action->stockOut($dto, $userId);
    }

    public function adjustStock(array $data, ?int $userId = null): InventoryTransaction
    {
        $dto = StockAdjustmentDTO::fromArray($data);

        return $this->action->adjustStock($dto, $userId);
    }

    public function getStockBalance(int $productId): int
    {
        return $this->action->getStockBalance($productId);
    }

    public function getAllStockBalances(): \Illuminate\Support\Collection
    {
        return $this->action->getAllStockBalances();
    }

    public function getProductLedger(int $productId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->action->getLedgerByProduct($productId, $perPage);
    }

    public function getLedger(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->action->getAllLedger($perPage);
    }
}