<?php

namespace App\Modules\Approval\Application\Services;

use App\Modules\Approval\Application\Actions\ApprovalAction;
use App\Modules\Approval\Application\DTOs\CreateApprovalRequestDTO;
use App\Modules\Approval\Application\DTOs\ApprovalDecisionDTO;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\Entities\ApprovalDecision;
use App\Modules\Approval\Domain\Entities\ApprovalActivityLog;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApprovalService
{
    public function __construct(
        private readonly ApprovalAction $action
    ) {}

    // Request methods
    public function getRequests(ApprovalFilterDTO $filter): LengthAwarePaginator
    {
        return $this->action->getAllRequests($filter->toArray());
    }

    public function getRequest(int $id): ApprovalRequest
    {
        return $this->action->getRequestById($id);
    }

    public function createRequest(array $data): ApprovalRequest
    {
        $dto = CreateApprovalRequestDTO::fromArray($data);

        return $this->action->createRequest($dto);
    }

    public function updateRequest(int $id, array $data): ApprovalRequest
    {
        return $this->action->updateRequest($id, $data);
    }

    public function deleteRequest(int $id): bool
    {
        return $this->action->deleteRequest($id);
    }

    public function getStatusCounts(): array
    {
        return $this->action->getStatusCounts();
    }

    // Decision methods
    public function approve(int $id, array $data): ApprovalRequest
    {
        $dto = ApprovalDecisionDTO::fromArray(array_merge($data, ['decision' => 'approved']));

        return $this->action->approve($id, $dto);
    }

    public function reject(int $id, array $data): ApprovalRequest
    {
        $dto = ApprovalDecisionDTO::fromArray(array_merge($data, ['decision' => 'rejected']));

        return $this->action->reject($id, $dto);
    }

    public function requestRevision(int $id, array $data): ApprovalRequest
    {
        $dto = ApprovalDecisionDTO::fromArray(array_merge($data, ['decision' => 'revision_requested']));

        return $this->action->requestRevision($id, $dto);
    }

    // Queue methods
    public function getPendingRequests(): Collection
    {
        return $this->action->getPendingRequests();
    }

    public function getQueueCounts(): array
    {
        return $this->action->getQueueCounts();
    }

    // History methods
    public function getRequestWithLogs(int $id): array
    {
        return $this->action->getRequestWithLogs($id);
    }

    // Activity Log methods
    public function getActivityLogs(int $requestId): Collection
    {
        return $this->action->getActivityLogs($requestId);
    }

    public function addComment(int $requestId, string $comment): ApprovalActivityLog
    {
        return $this->action->addComment($requestId, $comment);
    }

    // Helper methods
    public function hasPendingApproval(ApprovalType $type, int $referenceId): bool
    {
        return $this->action->hasPendingApproval($type, $referenceId);
    }

    public function getPendingApproval(ApprovalType $type, int $referenceId): ?ApprovalRequest
    {
        return $this->action->getPendingApproval($type, $referenceId);
    }
}