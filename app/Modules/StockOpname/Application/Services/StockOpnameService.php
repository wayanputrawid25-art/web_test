<?php

namespace App\Modules\StockOpname\Application\Services;

use App\Modules\StockOpname\Application\Actions\StockOpnameAction;
use App\Modules\StockOpname\Application\DTOs\CreateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\UpdateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\ChangeStatusDTO;
use App\Modules\StockOpname\Application\DTOs\CountItemDTO;
use App\Modules\StockOpname\Application\DTOs\StockOpnameFilterDTO;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use App\Modules\StockOpname\Domain\Entities\StockOpnameActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockOpnameService
{
    public function __construct(
        private readonly StockOpnameAction $action
    ) {}

    public function getSessions(StockOpnameFilterDTO $filter): LengthAwarePaginator
    {
        return $this->action->getAllSessions($filter->toArray());
    }

    public function getSession(int $id): StockOpnameSession
    {
        return $this->action->getSessionById($id);
    }

    public function createSession(array $data): StockOpnameSession
    {
        $dto = CreateStockOpnameSessionDTO::fromArray($data);

        return $this->action->createSession($dto);
    }

    public function updateSession(int $id, array $data): StockOpnameSession
    {
        $dto = UpdateStockOpnameSessionDTO::fromArray($data);

        return $this->action->updateSession($id, $dto);
    }

    public function deleteSession(int $id): bool
    {
        return $this->action->deleteSession($id);
    }

    public function changeStatus(int $id, array $data): StockOpnameSession
    {
        $dto = ChangeStatusDTO::fromArray($data);

        return $this->action->changeStatus($id, $dto);
    }

    public function getStatusCounts(): array
    {
        return $this->action->getStatusCounts();
    }

    public function assignCounters(int $sessionId, array $userIds): StockOpnameSession
    {
        return $this->action->assignCounters($sessionId, $userIds);
    }

    public function getAssignments(int $sessionId): Collection
    {
        return $this->action->getAssignments($sessionId);
    }

    public function getItems(int $sessionId): Collection
    {
        return $this->action->getItems($sessionId);
    }

    public function addProductsToSession(int $sessionId, array $productIds): Collection
    {
        return $this->action->addProductsToSession($sessionId, $productIds);
    }

    public function countItem(int $itemId, array $data): StockOpnameItem
    {
        $dto = CountItemDTO::fromArray($data);

        return $this->action->countItem($itemId, $dto);
    }

    public function submitForReview(int $sessionId): StockOpnameSession
    {
        return $this->action->submitForReview($sessionId);
    }

    public function approve(int $sessionId): StockOpnameSession
    {
        return $this->action->approve($sessionId);
    }

    public function requestRevision(int $sessionId, string $reason): StockOpnameSession
    {
        return $this->action->requestRevision($sessionId, $reason);
    }

    public function getActivityLogs(int $sessionId): Collection
    {
        return $this->action->getActivityLogs($sessionId);
    }

    public function getSessionWithLogs(int $sessionId): array
    {
        return $this->action->getSessionWithLogs($sessionId);
    }

    public function addComment(int $sessionId, string $comment): StockOpnameActivityLog
    {
        return $this->action->addComment($sessionId, $comment);
    }
}