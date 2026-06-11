<?php

namespace App\Modules\StockOpname\Infrastructure\Repositories;

use App\Modules\StockOpname\Domain\Contracts\StockOpnameRepositoryInterface;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\StockOpname\Domain\Entities\StockOpnameItem;
use App\Modules\StockOpname\Domain\Entities\StockOpnameAssignment;
use App\Modules\StockOpname\Domain\Entities\StockOpnameActivityLog;
use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use App\Modules\StockOpname\Infrastructure\Models\StockOpnameSession as SessionModel;
use App\Modules\StockOpname\Infrastructure\Models\StockOpnameItem as ItemModel;
use App\Modules\StockOpname\Infrastructure\Models\StockOpnameAssignment as AssignmentModel;
use App\Modules\StockOpname\Infrastructure\Models\StockOpnameActivityLog as ActivityLogModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockOpnameRepository implements StockOpnameRepositoryInterface
{
    public function findSessionById(int $id): ?StockOpnameSession
    {
        $model = SessionModel::with(['creator', 'task', 'items', 'assignments.user'])
            ->find($id);

        return $model ? StockOpnameSession::fromModel($model) : null;
    }

    public function findAllSessions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SessionModel::with(['creator', 'items']);

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        if (isset($filters['creator_id']) && $filters['creator_id']) {
            $query->where('creator_id', $filters['creator_id']);
        }

        if (isset($filters['my_assignments']) && $filters['my_assignments']) {
            $query->whereHas('assignments', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function createSession(array $data): StockOpnameSession
    {
        $data['code'] = $this->generateSessionCode();
        $model = SessionModel::create($data);

        return $this->findSessionById($model->id);
    }

    public function updateSession(int $id, array $data): StockOpnameSession
    {
        $model = SessionModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return $this->findSessionById($id);
    }

    public function deleteSession(int $id): bool
    {
        $model = SessionModel::findOrFail($id);

        return $model->delete();
    }

    public function countSessionsByStatus(): array
    {
        $counts = SessionModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $result = [];
        foreach (StockOpnameStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }

    public function generateSessionCode(): string
    {
        $prefix = 'SO';
        $date = now()->format('Ymd');
        $sequence = SessionModel::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function findItemById(int $id): ?StockOpnameItem
    {
        $model = ItemModel::with(['product', 'counter'])->find($id);

        return $model ? StockOpnameItem::fromModel($model) : null;
    }

    public function findItemsBySession(int $sessionId): Collection
    {
        return ItemModel::with(['product', 'counter', 'session'])
            ->where('stock_opname_session_id', $sessionId)
            ->get()
            ->map(fn ($model) => StockOpnameItem::fromModel($model));
    }

    public function createItem(array $data): StockOpnameItem
    {
        $model = ItemModel::create($data);

        return $this->findItemById($model->id);
    }

    public function updateItem(int $id, array $data): StockOpnameItem
    {
        $model = ItemModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return $this->findItemById($id);
    }

    public function deleteItem(int $id): bool
    {
        $model = ItemModel::findOrFail($id);

        return $model->delete();
    }

    public function createItemsBatch(int $sessionId, array $productIds): Collection
    {
        $items = collect();

        foreach ($productIds as $productId) {
            $product = \App\Modules\Product\Infrastructure\Models\Product::find($productId);
            
            if ($product) {
                $item = $this->createItem([
                    'stock_opname_session_id' => $sessionId,
                    'product_id' => $productId,
                    'system_quantity' => $product->quantity ?? 0,
                ]);
                $items->push($item);
            }
        }

        return $items;
    }

    public function findAssignmentsBySession(int $sessionId): Collection
    {
        return AssignmentModel::with(['user', 'assignedBy'])
            ->where('stock_opname_session_id', $sessionId)
            ->get()
            ->map(fn ($model) => StockOpnameAssignment::fromModel($model));
    }

    public function createAssignment(array $data): StockOpnameAssignment
    {
        $model = AssignmentModel::create($data);

        return StockOpnameAssignment::fromModel($model->load('user', 'assignedBy'));
    }

    public function deleteAssignment(int $id): bool
    {
        $model = AssignmentModel::findOrFail($id);

        return $model->delete();
    }

    public function assignCounters(int $sessionId, array $userIds): Collection
    {
        $assignments = collect();

        foreach ($userIds as $userId) {
            $assignment = $this->createAssignment([
                'stock_opname_session_id' => $sessionId,
                'user_id' => $userId,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
            $assignments->push($assignment);
        }

        return $assignments;
    }

    public function getActivityLogs(int $sessionId): Collection
    {
        return ActivityLogModel::with('user')
            ->where('stock_opname_session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => StockOpnameActivityLog::fromModel($model));
    }

    public function createActivityLog(array $data): StockOpnameActivityLog
    {
        $model = ActivityLogModel::create($data);

        return StockOpnameActivityLog::fromModel($model->load('user'));
    }
}