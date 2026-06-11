<?php

namespace App\Modules\Approval\Infrastructure\Repositories;

use App\Modules\Approval\Domain\Contracts\ApprovalRepositoryInterface;
use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\Entities\ApprovalDecision;
use App\Modules\Approval\Domain\Entities\ApprovalActivityLog;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use App\Modules\Approval\Infrastructure\Models\ApprovalRequest as ApprovalRequestModel;
use App\Modules\Approval\Infrastructure\Models\ApprovalDecision as ApprovalDecisionModel;
use App\Modules\Approval\Infrastructure\Models\ApprovalActivityLog as ApprovalActivityLogModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    // Request methods
    public function findRequestById(int $id): ?ApprovalRequest
    {
        $model = ApprovalRequestModel::with(['requester', 'approver', 'decision', 'activityLogs.user'])
            ->find($id);

        return $model ? ApprovalRequest::fromModel($model) : null;
    }

    public function findAllRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ApprovalRequestModel::with(['requester', 'approver']);

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        if (isset($filters['requester_id']) && $filters['requester_id']) {
            $query->where('requester_id', $filters['requester_id']);
        }

        if (isset($filters['approver_id']) && $filters['approver_id']) {
            $query->where('approver_id', $filters['approver_id']);
        }

        if (isset($filters['my_requests']) && $filters['my_requests']) {
            $query->byRequester(auth()->id());
        }

        if (isset($filters['pending_for_me']) && $filters['pending_for_me']) {
            $query->pending()
                  ->where('requester_id', '!=', auth()->id());
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function createRequest(array $data): ApprovalRequest
    {
        $data['code'] = $this->generateRequestCode();
        $model = ApprovalRequestModel::create($data);

        return $this->findRequestById($model->id);
    }

    public function updateRequest(int $id, array $data): ApprovalRequest
    {
        $model = ApprovalRequestModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return $this->findRequestById($id);
    }

    public function deleteRequest(int $id): bool
    {
        $model = ApprovalRequestModel::findOrFail($id);

        return $model->delete();
    }

    public function countRequestsByStatus(): array
    {
        $counts = ApprovalRequestModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $result = [];
        foreach (ApprovalStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }

    public function generateRequestCode(): string
    {
        $prefix = 'APR';
        $date = now()->format('Ymd');
        $sequence = ApprovalRequestModel::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function findPendingRequests(): Collection
    {
        return ApprovalRequestModel::with(['requester'])
            ->pending()
            ->where('requester_id', '!=', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($model) => ApprovalRequest::fromModel($model));
    }

    // Decision methods
    public function findDecisionByRequest(int $requestId): ?ApprovalDecision
    {
        $model = ApprovalDecisionModel::with('approver')->where('approval_request_id', $requestId)->first();

        return $model ? ApprovalDecision::fromModel($model) : null;
    }

    public function createDecision(array $data): ApprovalDecision
    {
        $model = ApprovalDecisionModel::create($data);

        return ApprovalDecision::fromModel($model->load('approver'));
    }

    // Activity Log methods
    public function getActivityLogs(int $requestId): Collection
    {
        return ApprovalActivityLogModel::with('user')
            ->where('approval_request_id', $requestId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => ApprovalActivityLog::fromModel($model));
    }

    public function createActivityLog(array $data): ApprovalActivityLog
    {
        $model = ApprovalActivityLogModel::create($data);

        return ApprovalActivityLog::fromModel($model->load('user'));
    }

    // Check for existing pending request
    public function hasPendingRequest(ApprovalType $type, int $referenceId): bool
    {
        return ApprovalRequestModel::where('type', $type->value)
            ->where('reference_id', $referenceId)
            ->pending()
            ->exists();
    }

    public function findByReference(ApprovalType $type, int $referenceId): ?ApprovalRequest
    {
        $model = ApprovalRequestModel::with(['requester', 'approver', 'decision'])
            ->where('type', $type->value)
            ->where('reference_id', $referenceId)
            ->first();

        return $model ? ApprovalRequest::fromModel($model) : null;
    }
}