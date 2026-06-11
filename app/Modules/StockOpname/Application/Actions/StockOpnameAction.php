<?php

namespace App\Modules\StockOpname\Application\Actions;

use App\Modules\StockOpname\Application\DTOs\CreateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\UpdateStockOpnameSessionDTO;
use App\Modules\StockOpname\Application\DTOs\ChangeStatusDTO;
use App\Modules\StockOpname\Application\DTOs\CountItemDTO;
use App\Modules\StockOpname\Domain\Contracts\StockOpnameRepositoryInterface;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use App\Modules\StockOpname\Domain\Entities\StockOpnameActivityLog;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use App\Modules\StockOpname\Exceptions\StockOpnameNotFoundException;
use App\Modules\StockOpname\Exceptions\InvalidStockOpnameTransitionException;
use App\Modules\StockOpname\Exceptions\ItemNotFoundException;
use App\Modules\StockOpname\Exceptions\NegativeQuantityException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockOpnameAction
{
    public function __construct(
        private readonly StockOpnameRepositoryInterface $repository
    ) {}

    // Session methods
    public function getAllSessions(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->findAllSessions($filters);
    }

    public function getSessionById(int $id): StockOpnameSession
    {
        $session = $this->repository->findSessionById($id);

        if (!$session) {
            throw new StockOpnameNotFoundException($id);
        }

        return $session;
    }

    public function createSession(CreateStockOpnameSessionDTO $dto): StockOpnameSession
    {
        $session = $this->repository->createSession($dto->toArray());

        // Add products if provided
        if (!empty($dto->productIds)) {
            $this->addProductsToSession($session->id, $dto->productIds);
            $session = $this->getSessionById($session->id);
        }

        $this->logActivity($session->id, 'created', null, 'created', "Stock opname session created");

        return $session;
    }

    public function updateSession(int $id, UpdateStockOpnameSessionDTO $dto): StockOpnameSession
    {
        $session = $this->repository->findSessionById($id);

        if (!$session) {
            throw new StockOpnameNotFoundException($id);
        }

        if (!$session->status->isEditable()) {
            throw new InvalidStockOpnameTransitionException($session->status->value, 'update');
        }

        $session = $this->repository->updateSession($id, $dto->toArray());

        $this->logActivity($session->id, 'updated', null, null, "Session details updated");

        return $session;
    }

    public function deleteSession(int $id): bool
    {
        $session = $this->repository->findSessionById($id);

        if (!$session) {
            throw new StockOpnameNotFoundException($id);
        }

        if ($session->status !== StockOpnameStatus::CREATED) {
            throw new InvalidStockOpnameTransitionException($session->status->value, 'delete');
        }

        return $this->repository->deleteSession($id);
    }

    public function changeStatus(int $id, ChangeStatusDTO $dto): StockOpnameSession
    {
        $session = $this->repository->findSessionById($id);

        if (!$session) {
            throw new StockOpnameNotFoundException($id);
        }

        $newStatus = StockOpnameStatus::from($dto->newStatus);

        if (!$session->canTransitionTo($newStatus)) {
            throw new InvalidStockOpnameTransitionException($session->status->value, $newStatus->value);
        }

        $oldStatus = $session->status->value;
        $session = $this->repository->updateSession($id, ['status' => $newStatus->value]);

        $this->logActivity($session->id, 'status_changed', $oldStatus, $newStatus->value, $dto->notes);

        return $session;
    }

    public function getStatusCounts(): array
    {
        return $this->repository->countSessionsByStatus();
    }

    // Assignment methods
    public function assignCounters(int $sessionId, array $userIds): StockOpnameSession
    {
        $session = $this->repository->findSessionById($sessionId);

        if (!$session) {
            throw new StockOpnameNotFoundException($sessionId);
        }

        if ($session->status !== StockOpnameStatus::CREATED) {
            throw new InvalidStockOpnameTransitionException($session->status->value, 'assign');
        }

        $this->repository->assignCounters($sessionId, $userIds);

        $this->logActivity($sessionId, 'assigned', null, implode(',', $userIds), "Assigned counters");

        // Update status to assigned
        return $this->changeStatus($sessionId, ChangeStatusDTO::fromArray([
            'status' => 'assigned',
            'notes' => 'Counters assigned'
        ]));
    }

    public function getAssignments(int $sessionId): Collection
    {
        return $this->repository->findAssignmentsBySession($sessionId);
    }

    // Item methods
    public function getItems(int $sessionId): Collection
    {
        return $this->repository->findItemsBySession($sessionId);
    }

    public function addProductsToSession(int $sessionId, array $productIds): Collection
    {
        $session = $this->repository->findSessionById($sessionId);

        if (!$session) {
            throw new StockOpnameNotFoundException($sessionId);
        }

        if (!$session->status->isEditable()) {
            throw new InvalidStockOpnameTransitionException($session->status->value, 'add_products');
        }

        return $this->repository->createItemsBatch($sessionId, $productIds);
    }

    public function countItem(int $itemId, CountItemDTO $dto): StockOpnameItem
    {
        $item = $this->repository->findItemById($itemId);

        if (!$item) {
            throw new ItemNotFoundException($itemId);
        }

        if ($dto->countedQuantity < 0) {
            throw new NegativeQuantityException();
        }

        $item = $this->repository->updateItem($itemId, $dto->toArray());

        $this->logActivity(
            $item->sessionId,
            'item_counted',
            (string) $item->systemQuantity,
            (string) $dto->countedQuantity,
            "Product {$item->productSku} counted"
        );

        return $item;
    }

    public function submitForReview(int $sessionId): StockOpnameSession
    {
        $session = $this->repository->findSessionById($sessionId);

        if (!$session) {
            throw new StockOpnameNotFoundException($sessionId);
        }

        // Check all items are counted
        $items = $this->getItems($sessionId);
        $unCounted = $items->filter(fn ($item) => $item->countedQuantity === null);

        if ($unCounted->isNotEmpty()) {
            throw new \Exception("Cannot submit: {$unCounted->count()} items still not counted");
        }

        return $this->changeStatus($sessionId, ChangeStatusDTO::fromArray([
            'status' => 'submitted',
            'notes' => 'Submitted for review'
        ]));
    }

    public function approve(int $sessionId): StockOpnameSession
    {
        return $this->changeStatus($sessionId, ChangeStatusDTO::fromArray([
            'status' => 'approved',
            'notes' => 'Approved'
        ]));
    }

    public function requestRevision(int $sessionId, string $reason): StockOpnameSession
    {
        return $this->changeStatus($sessionId, ChangeStatusDTO::fromArray([
            'status' => 'counting',
            'notes' => "Revision requested: {$reason}"
        ]));
    }

    // Activity Log methods
    public function getActivityLogs(int $sessionId): Collection
    {
        return $this->repository->getActivityLogs($sessionId);
    }

    public function getSessionWithLogs(int $sessionId): array
    {
        $session = $this->getSessionById($sessionId);
        $logs = $this->getActivityLogs($sessionId);
        $items = $this->getItems($sessionId);
        $assignments = $this->getAssignments($sessionId);

        return [
            'session' => $session,
            'activityLogs' => $logs,
            'items' => $items,
            'assignments' => $assignments,
        ];
    }

    public function addComment(int $sessionId, string $comment): StockOpnameActivityLog
    {
        $session = $this->repository->findSessionById($sessionId);

        if (!$session) {
            throw new StockOpnameNotFoundException($sessionId);
        }

        return $this->logActivity($sessionId, 'comment', null, null, $comment);
    }

    private function logActivity(
        int $sessionId,
        string $action,
        ?string $oldValue,
        ?string $newValue,
        ?string $notes = null
    ): StockOpnameActivityLog {
        return $this->repository->createActivityLog([
            'stock_opname_session_id' => $sessionId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => auth()->id(),
            'notes' => $notes,
        ]);
    }
}