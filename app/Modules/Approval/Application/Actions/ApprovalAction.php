<?php

namespace App\Modules\Approval\Application\Actions;

use App\Modules\Approval\Application\DTOs\CreateApprovalRequestDTO;
use App\Modules\Approval\Application\DTOs\ApprovalDecisionDTO;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\Approval\Domain\Contracts\ApprovalRepositoryInterface;
use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\Entities\ApprovalDecision;
use App\Modules\Approval\Domain\Entities\ApprovalActivityLog;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use App\Modules\Approval\Exceptions\ApprovalNotFoundException;
use App\Modules\Approval\Exceptions\DuplicateApprovalException;
use App\Modules\Approval\Exceptions\UnauthorizedApprovalException;
use App\Modules\Approval\Exceptions\AlreadyProcessedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApprovalAction
{
    public function __construct(
        private readonly ApprovalRepositoryInterface $repository
    ) {}

    // Request methods
    public function getAllRequests(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->findAllRequests($filters);
    }

    public function getRequestById(int $id): ApprovalRequest
    {
        $request = $this->repository->findRequestById($id);

        if (!$request) {
            throw new ApprovalNotFoundException($id);
        }

        return $request;
    }

    public function createRequest(CreateApprovalRequestDTO $dto): ApprovalRequest
    {
        $type = ApprovalType::from($dto->type);

        // Check for duplicate pending request
        if ($this->repository->hasPendingRequest($type, $dto->referenceId)) {
            throw new DuplicateApprovalException($type->value, $dto->referenceId);
        }

        $request = $this->repository->createRequest($dto->toArray());

        $this->logActivity($request->id, 'created', null, 'pending', 'Request created');

        return $request;
    }

    public function updateRequest(int $id, array $data): ApprovalRequest
    {
        $request = $this->repository->findRequestById($id);

        if (!$request) {
            throw new ApprovalNotFoundException($id);
        }

        if (!$request->isPending()) {
            throw new AlreadyProcessedException();
        }

        $request = $this->repository->updateRequest($id, $data);

        $this->logActivity($id, 'updated', null, null, 'Request details updated');

        return $request;
    }

    public function deleteRequest(int $id): bool
    {
        $request = $this->repository->findRequestById($id);

        if (!$request) {
            throw new ApprovalNotFoundException($id);
        }

        if (!$request->isPending()) {
            throw new AlreadyProcessedException();
        }

        // Check if requester is deleting their own request
        if ($request->requesterId !== auth()->id()) {
            throw new UnauthorizedApprovalException();
        }

        return $this->repository->deleteRequest($id);
    }

    public function getStatusCounts(): array
    {
        return $this->repository->countRequestsByStatus();
    }

    // Decision methods
    public function approve(int $id, ApprovalDecisionDTO $dto): ApprovalRequest
    {
        $request = $this->validateAction($id, 'approve');

        // Create decision
        $this->repository->createDecision([
            'approval_request_id' => $id,
            'decision' => 'approved',
            'approver_id' => auth()->id(),
            'comments' => $dto->comments,
        ]);

        // Update request status
        $request = $this->repository->updateRequest($id, [
            'status' => ApprovalStatus::APPROVED->value,
            'approver_id' => auth()->id(),
            'processed_at' => now(),
        ]);

        $this->logActivity($id, 'approved', 'pending', 'approved', $dto->comments);

        // Trigger related actions based on type
        $this->processApprovalActions($request);

        return $request;
    }

    public function reject(int $id, ApprovalDecisionDTO $dto): ApprovalRequest
    {
        $request = $this->validateAction($id, 'reject');

        if (empty($dto->comments)) {
            throw new \InvalidArgumentException('Comments are required when rejecting a request');
        }

        // Create decision
        $this->repository->createDecision([
            'approval_request_id' => $id,
            'decision' => 'rejected',
            'approver_id' => auth()->id(),
            'comments' => $dto->comments,
        ]);

        // Update request status
        $request = $this->repository->updateRequest($id, [
            'status' => ApprovalStatus::REJECTED->value,
            'approver_id' => auth()->id(),
            'processed_at' => now(),
        ]);

        $this->logActivity($id, 'rejected', 'pending', 'rejected', $dto->comments);

        // Trigger related actions based on type
        $this->processRejectionActions($request);

        return $request;
    }

    public function requestRevision(int $id, ApprovalDecisionDTO $dto): ApprovalRequest
    {
        $request = $this->validateAction($id, 'request_revision');

        if (empty($dto->comments)) {
            throw new \InvalidArgumentException('Comments are required when requesting revision');
        }

        // Create decision
        $this->repository->createDecision([
            'approval_request_id' => $id,
            'decision' => 'revision_requested',
            'approver_id' => auth()->id(),
            'comments' => $dto->comments,
        ]);

        // Update request status
        $request = $this->repository->updateRequest($id, [
            'status' => ApprovalStatus::REVISION_REQUESTED->value,
            'approver_id' => auth()->id(),
            'processed_at' => now(),
        ]);

        $this->logActivity($id, 'revision_requested', 'pending', 'revision_requested', $dto->comments);

        // Trigger related actions based on type
        $this->processRevisionActions($request);

        return $request;
    }

    // Queue methods
    public function getPendingRequests(): Collection
    {
        return $this->repository->findPendingRequests();
    }

    public function getQueueCounts(): array
    {
        return [
            'pending' => $this->repository->countRequestsByStatus()['pending'] ?? 0,
        ];
    }

    // History methods
    public function getRequestWithLogs(int $id): array
    {
        $request = $this->getRequestById($id);
        $logs = $this->repository->getActivityLogs($id);
        $decision = $this->repository->findDecisionByRequest($id);

        return [
            'request' => $request,
            'activityLogs' => $logs,
            'decision' => $decision,
        ];
    }

    // Activity Log methods
    public function getActivityLogs(int $requestId): Collection
    {
        return $this->repository->getActivityLogs($requestId);
    }

    public function addComment(int $requestId, string $comment): ApprovalActivityLog
    {
        $request = $this->repository->findRequestById($requestId);

        if (!$request) {
            throw new ApprovalNotFoundException($requestId);
        }

        return $this->logActivity($requestId, 'commented', null, null, $comment);
    }

    // Helper methods
    private function validateAction(int $id, string $action): ApprovalRequest
    {
        $request = $this->repository->findRequestById($id);

        if (!$request) {
            throw new ApprovalNotFoundException($id);
        }

        if (!$request->canBeProcessedBy(auth()->id())) {
            throw new UnauthorizedApprovalException();
        }

        return $request;
    }

    private function logActivity(
        int $requestId,
        string $action,
        ?string $oldValue,
        ?string $newValue,
        ?string $notes = null
    ): ApprovalActivityLog {
        return $this->repository->createActivityLog([
            'approval_request_id' => $requestId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => auth()->id(),
            'notes' => $notes,
        ]);
    }

    private function processApprovalActions(ApprovalRequest $request): void
    {
        // Process based on type
        if ($request->type === ApprovalType::STOCK_OPNAME) {
            // Update stock opname status to approved
            app(\App\Modules\StockOpname\Application\Services\StockOpnameService::class)
                ->approve($request->referenceId);
        }
        // Add other type processing as needed
    }

    private function processRejectionActions(ApprovalRequest $request): void
    {
        // Rejection actions can be implemented here
    }

    private function processRevisionActions(ApprovalRequest $request): void
    {
        if ($request->type === ApprovalType::STOCK_OPNAME) {
            // Request revision on stock opname
            app(\App\Modules\StockOpname\Application\Services\StockOpnameService::class)
                ->requestRevision($request->referenceId, 'Revision requested via approval');
        }
    }

    // Check if reference has pending approval
    public function hasPendingApproval(ApprovalType $type, int $referenceId): bool
    {
        return $this->repository->hasPendingRequest($type, $referenceId);
    }

    // Get pending approval for reference
    public function getPendingApproval(ApprovalType $type, int $referenceId): ?ApprovalRequest
    {
        return $this->repository->findByReference($type, $referenceId);
    }
}