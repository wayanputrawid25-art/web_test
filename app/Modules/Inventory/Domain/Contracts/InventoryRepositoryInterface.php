<?php

namespace App\Modules\Inventory\Domain\Contracts;

use App\Modules\Inventory\Domain\Entities\InventoryTransaction;
use App\Modules\Inventory\Domain\Entities\StockLedger;
use Illuminate\Support\Collection;

interface InventoryRepositoryInterface
{
    // Transactions
    public function createTransaction(array $data): InventoryTransaction;
    public function getTransactionById(int $id): ?InventoryTransaction;
    public function getTransactions(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    // Stock Ledger
    public function getStockBalance(int $productId): int;
    public function getStockBalances(): Collection;
    public function createLedgerEntry(array $data): StockLedger;
    public function getLedgerByProduct(int $productId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function getAllLedger(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
}