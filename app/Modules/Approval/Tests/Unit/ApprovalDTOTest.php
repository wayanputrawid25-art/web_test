<?php

namespace App\Modules\Approval\Tests\Unit;

use App\Modules\Approval\Application\DTOs\CreateApprovalRequestDTO;
use App\Modules\Approval\Application\DTOs\ApprovalDecisionDTO;
use App\Modules\Approval\Application\DTOs\ApprovalFilterDTO;
use PHPUnit\Framework\TestCase;

class ApprovalDTOTest extends TestCase
{
    public function test_create_request_dto_from_array(): void
    {
        $data = [
            'type' => 'stock_opname',
            'reference_id' => 10,
            'title' => 'Monthly Stock Opname',
            'description' => 'Approval for January stock opname',
            'approver_id' => 2,
            'notes' => 'Urgent',
        ];

        $dto = CreateApprovalRequestDTO::fromArray($data);

        $this->assertEquals('stock_opname', $dto->type);
        $this->assertEquals(10, $dto->referenceId);
        $this->assertEquals('Monthly Stock Opname', $dto->title);
        $this->assertEquals('Approval for January stock opname', $dto->description);
        $this->assertEquals(2, $dto->approverId);
        $this->assertEquals('Urgent', $dto->notes);
    }

    public function test_create_request_dto_to_array(): void
    {
        $dto = new CreateApprovalRequestDTO(
            type: 'stock_opname',
            referenceId: 10,
            title: 'Test Approval',
            description: 'Description',
            approverId: 2,
            notes: 'Notes',
        );

        $array = $dto->toArray();

        $this->assertEquals('stock_opname', $array['type']);
        $this->assertEquals(10, $array['reference_id']);
        $this->assertEquals('Test Approval', $array['title']);
        $this->assertEquals('Description', $array['description']);
        $this->assertEquals(2, $array['approver_id']);
        $this->assertEquals('Notes', $array['notes']);
        $this->assertEquals('pending', $array['status']);
        $this->assertArrayHasKey('requester_id', $array);
    }

    public function test_decision_dto(): void
    {
        $dto = ApprovalDecisionDTO::fromArray([
            'decision' => 'approved',
            'comments' => 'Looks good',
        ]);

        $this->assertEquals('approved', $dto->decision);
        $this->assertEquals('Looks good', $dto->comments);

        $array = $dto->toArray();
        $this->assertEquals(['decision' => 'approved', 'comments' => 'Looks good'], $array);
    }

    public function test_filter_dto_defaults(): void
    {
        $dto = ApprovalFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->type);
        $this->assertNull($dto->status);
        $this->assertFalse($dto->myRequests);
        $this->assertFalse($dto->pendingForMe);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_filter_dto_with_values(): void
    {
        $dto = ApprovalFilterDTO::fromArray([
            'search' => 'stock opname',
            'type' => 'stock_opname',
            'status' => 'pending',
            'my_requests' => true,
            'pending_for_me' => true,
            'per_page' => 25,
        ]);

        $this->assertEquals('stock opname', $dto->search);
        $this->assertEquals('stock_opname', $dto->type);
        $this->assertEquals('pending', $dto->status);
        $this->assertTrue($dto->myRequests);
        $this->assertTrue($dto->pendingForMe);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_filter_dto_to_array(): void
    {
        $dto = new ApprovalFilterDTO(
            search: 'test',
            type: 'stock_opname',
            status: 'pending',
            myRequests: true,
            pendingForMe: false,
            perPage: 10,
        );

        $array = $dto->toArray();

        $this->assertEquals('test', $array['search']);
        $this->assertEquals('stock_opname', $array['type']);
        $this->assertEquals('pending', $array['status']);
        $this->assertTrue($array['my_requests']);
        $this->assertArrayNotHasKey('pending_for_me', $array);
    }
}