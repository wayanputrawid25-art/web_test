<?php

namespace App\Modules\Approval\Tests\Unit;

use App\Modules\Approval\Domain\Entities\ApprovalRequest;
use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use PHPUnit\Framework\TestCase;

class ApprovalRequestTest extends TestCase
{
    public function test_can_create_request(): void
    {
        $request = new ApprovalRequest(
            id: 1,
            code: 'APR-20240101-0001',
            type: ApprovalType::STOCK_OPNAME,
            status: ApprovalStatus::PENDING,
            referenceId: 10,
            title: 'Monthly Stock Opname Approval',
            description: 'Approval for January stock opname',
            requesterId: 1,
            requesterName: 'John Doe',
            approverId: null,
            approverName: null,
            notes: 'Urgent approval needed',
            processedAt: null,
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $this->assertEquals(1, $request->id);
        $this->assertEquals('APR-20240101-0001', $request->code);
        $this->assertEquals(ApprovalType::STOCK_OPNAME, $request->type);
        $this->assertEquals(ApprovalStatus::PENDING, $request->status);
        $this->assertEquals(10, $request->referenceId);
        $this->assertEquals('Monthly Stock Opname Approval', $request->title);
        $this->assertEquals(1, $request->requesterId);
        $this->assertNull($request->approverId);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $request = new ApprovalRequest(
            id: 1, code: 'APR-001', type: ApprovalType::STOCK_OPNAME, status: ApprovalStatus::PENDING,
            referenceId: 10, title: 'Test', description: 'Desc', requesterId: 1, requesterName: 'John',
            approverId: null, approverName: null, notes: null, processedAt: null,
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $array = $request->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals('APR-001', $array['code']);
        $this->assertEquals('stock_opname', $array['type']);
        $this->assertEquals('pending', $array['status']);
        $this->assertEquals(10, $array['reference_id']);
        $this->assertEquals('Test', $array['title']);
    }

    public function test_is_pending(): void
    {
        $pendingRequest = new ApprovalRequest(
            id: 1, code: 'APR-001', type: ApprovalType::STOCK_OPNAME, status: ApprovalStatus::PENDING,
            referenceId: 1, title: 'Test', description: null, requesterId: 1, requesterName: 'John',
            approverId: null, approverName: null, notes: null, processedAt: null,
            createdAt: null, updatedAt: null,
        );

        $approvedRequest = new ApprovalRequest(
            id: 2, code: 'APR-002', type: ApprovalType::STOCK_OPNAME, status: ApprovalStatus::APPROVED,
            referenceId: 1, title: 'Test', description: null, requesterId: 1, requesterName: 'John',
            approverId: 2, approverName: 'Jane', notes: null, processedAt: null,
            createdAt: null, updatedAt: null,
        );

        $this->assertTrue($pendingRequest->isPending());
        $this->assertFalse($approvedRequest->isPending());
    }

    public function test_can_be_processed_by_different_user(): void
    {
        $request = new ApprovalRequest(
            id: 1, code: 'APR-001', type: ApprovalType::STOCK_OPNAME, status: ApprovalStatus::PENDING,
            referenceId: 1, title: 'Test', description: null, requesterId: 1, requesterName: 'John',
            approverId: null, approverName: null, notes: null, processedAt: null,
            createdAt: null, updatedAt: null,
        );

        // User 2 can process user 1's request
        $this->assertTrue($request->canBeProcessedBy(2));
        
        // User 1 cannot process their own request
        $this->assertFalse($request->canBeProcessedBy(1));
    }

    public function test_cannot_be_processed_when_not_pending(): void
    {
        $request = new ApprovalRequest(
            id: 1, code: 'APR-001', type: ApprovalType::STOCK_OPNAME, status: ApprovalStatus::APPROVED,
            referenceId: 1, title: 'Test', description: null, requesterId: 1, requesterName: 'John',
            approverId: 2, approverName: 'Jane', notes: null, processedAt: null,
            createdAt: null, updatedAt: null,
        );

        // Even different user cannot process already approved request
        $this->assertFalse($request->canBeProcessedBy(3));
    }
}