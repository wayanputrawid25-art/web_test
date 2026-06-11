<?php

namespace App\Modules\Approval\Domain\Contracts;

use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\Entities\ApprovalDecision;
use App\Modules\Approval\Domain\Entities\ApprovalActivityLog;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ApprovalRepositoryInterface
{
    // Request methods
    public function findRequestById(int $id): ?ApprovalRequest;
    public function findAllRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function createRequest(array $data): ApprovalRequest;
    public function updateRequest(int $id, array $data): ApprovalRequest;
    public function deleteRequest(int $id): bool;
    public function countRequestsByStatus(): array;
    public function generateRequestCode(): string;
    public function findPendingRequests(): Collection;

    // Decision methods
    public function findDecisionByRequest(int $requestId): ?ApprovalDecision;
    public function createDecision(array $data): ApprovalDecision;

    // Activity Log methods
    public function getActivityLogs(int $requestId): Collection;
    public function createActivityLog(array $data): ApprovalActivityLog;

    // Check for existing pending request
    public function hasPendingRequest(ApprovalType $type, int $referenceId): bool;
    public function findByReference(ApprovalType $type, int $referenceId): ?ApprovalRequest;
}