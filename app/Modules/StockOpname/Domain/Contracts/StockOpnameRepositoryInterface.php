<?php

namespace App\Modules\StockOpname\Domain\Contracts;

use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use App\Modules\StockOpname\Domain\Entities\StockOpnameAssignment;
use App\Modules\StockOpname\Domain\Entities\StockOpnameActivityLog;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StockOpnameRepositoryInterface
{
    // Session methods
    public function findSessionById(int $id): ?StockOpnameSession;
    public function findAllSessions(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function createSession(array $data): StockOpnameSession;
    public function updateSession(int $id, array $data): StockOpnameSession;
    public function deleteSession(int $id): bool;
    public function countSessionsByStatus(): array;
    public function generateSessionCode(): string;

    // Item methods
    public function findItemById(int $id): ?StockOpnameItem;
    public function findItemsBySession(int $sessionId): Collection;
    public function createItem(array $data): StockOpnameItem;
    public function updateItem(int $id, array $data): StockOpnameItem;
    public function deleteItem(int $id): bool;
    public function createItemsBatch(int $sessionId, array $productIds): Collection;

    // Assignment methods
    public function findAssignmentsBySession(int $sessionId): Collection;
    public function createAssignment(array $data): StockOpnameAssignment;
    public function deleteAssignment(int $id): bool;
    public function assignCounters(int $sessionId, array $userIds): Collection;

    // Activity Log methods
    public function getActivityLogs(int $sessionId): Collection;
    public function createActivityLog(array $data): StockOpnameActivityLog;
}