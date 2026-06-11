<?php

namespace App\Modules\Approval\Tests\Unit;

use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use PHPUnit\Framework\TestCase;

class ApprovalStatusTest extends TestCase
{
    public function test_all_statuses_have_correct_values(): void
    {
        $this->assertEquals('pending', ApprovalStatus::PENDING->value);
        $this->assertEquals('approved', ApprovalStatus::APPROVED->value);
        $this->assertEquals('rejected', ApprovalStatus::REJECTED->value);
        $this->assertEquals('revision_requested', ApprovalStatus::REVISION_REQUESTED->value);
    }

    public function test_labels_are_correct(): void
    {
        $this->assertEquals('Pending', ApprovalStatus::PENDING->label());
        $this->assertEquals('Approved', ApprovalStatus::APPROVED->label());
        $this->assertEquals('Rejected', ApprovalStatus::REJECTED->label());
        $this->assertEquals('Revision Requested', ApprovalStatus::REVISION_REQUESTED->label());
    }

    public function test_colors_are_correct(): void
    {
        $this->assertEquals('yellow', ApprovalStatus::PENDING->color());
        $this->assertEquals('green', ApprovalStatus::APPROVED->color());
        $this->assertEquals('red', ApprovalStatus::REJECTED->color());
        $this->assertEquals('orange', ApprovalStatus::REVISION_REQUESTED->color());
    }

    public function test_is_pending(): void
    {
        $this->assertTrue(ApprovalStatus::PENDING->isPending());
        $this->assertFalse(ApprovalStatus::APPROVED->isPending());
        $this->assertFalse(ApprovalStatus::REJECTED->isPending());
        $this->assertFalse(ApprovalStatus::REVISION_REQUESTED->isPending());
    }

    public function test_is_final(): void
    {
        $this->assertFalse(ApprovalStatus::PENDING->isFinal());
        $this->assertTrue(ApprovalStatus::APPROVED->isFinal());
        $this->assertTrue(ApprovalStatus::REJECTED->isFinal());
        $this->assertFalse(ApprovalStatus::REVISION_REQUESTED->isFinal());
    }

    public function test_can_be_actioned_by_same_user(): void
    {
        // Same user cannot approve their own request
        $this->assertFalse(ApprovalStatus::PENDING->canBeActionedBy(1, 1));
    }

    public function test_can_be_actioned_by_different_user(): void
    {
        // Different user can approve
        $this->assertTrue(ApprovalStatus::PENDING->canBeActionedBy(2, 1));
    }

    public function test_cannot_be_actioned_when_not_pending(): void
    {
        $this->assertFalse(ApprovalStatus::APPROVED->canBeActionedBy(2, 1));
        $this->assertFalse(ApprovalStatus::REJECTED->canBeActionedBy(2, 1));
        $this->assertFalse(ApprovalStatus::REVISION_REQUESTED->canBeActionedBy(2, 1));
    }
}