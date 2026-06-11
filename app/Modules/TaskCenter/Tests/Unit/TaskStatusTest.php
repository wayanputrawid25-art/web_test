<?php

namespace App\Modules\TaskCenter\Tests\Unit;

use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function test_all_statuses_have_correct_values(): void
    {
        $this->assertEquals('draft', TaskStatus::DRAFT->value);
        $this->assertEquals('assigned', TaskStatus::ASSIGNED->value);
        $this->assertEquals('in_progress', TaskStatus::IN_PROGRESS->value);
        $this->assertEquals('review', TaskStatus::REVIEW->value);
        $this->assertEquals('approved', TaskStatus::APPROVED->value);
        $this->assertEquals('closed', TaskStatus::CLOSED->value);
    }

    public function test_labels_are_correct(): void
    {
        $this->assertEquals('Draft', TaskStatus::DRAFT->label());
        $this->assertEquals('Assigned', TaskStatus::ASSIGNED->label());
        $this->assertEquals('In Progress', TaskStatus::IN_PROGRESS->label());
        $this->assertEquals('Review', TaskStatus::REVIEW->label());
        $this->assertEquals('Approved', TaskStatus::APPROVED->label());
        $this->assertEquals('Closed', TaskStatus::CLOSED->label());
    }

    public function test_can_create_from_string(): void
    {
        $status = TaskStatus::from('draft');
        $this->assertEquals(TaskStatus::DRAFT, $status);
    }

    public function test_transition_rules_draft_to_assigned(): void
    {
        $this->assertTrue(TaskStatus::DRAFT->canTransitionTo(TaskStatus::ASSIGNED));
        $this->assertFalse(TaskStatus::DRAFT->canTransitionTo(TaskStatus::IN_PROGRESS));
        $this->assertFalse(TaskStatus::DRAFT->canTransitionTo(TaskStatus::CLOSED));
    }

    public function test_transition_rules_assigned_to_in_progress(): void
    {
        $this->assertTrue(TaskStatus::ASSIGNED->canTransitionTo(TaskStatus::IN_PROGRESS));
        $this->assertFalse(TaskStatus::ASSIGNED->canTransitionTo(TaskStatus::REVIEW));
        $this->assertFalse(TaskStatus::ASSIGNED->canTransitionTo(TaskStatus::CLOSED));
    }

    public function test_transition_rules_in_progress_to_review_or_closed(): void
    {
        $this->assertTrue(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::REVIEW));
        $this->assertTrue(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::CLOSED));
        $this->assertFalse(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::APPROVED));
        $this->assertFalse(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::DRAFT));
    }

    public function test_transition_rules_review_to_approved_or_in_progress(): void
    {
        $this->assertTrue(TaskStatus::REVIEW->canTransitionTo(TaskStatus::APPROVED));
        $this->assertTrue(TaskStatus::REVIEW->canTransitionTo(TaskStatus::IN_PROGRESS));
        $this->assertFalse(TaskStatus::REVIEW->canTransitionTo(TaskStatus::CLOSED));
    }

    public function test_transition_rules_approved_to_in_progress_or_closed(): void
    {
        $this->assertTrue(TaskStatus::APPROVED->canTransitionTo(TaskStatus::IN_PROGRESS));
        $this->assertTrue(TaskStatus::APPROVED->canTransitionTo(TaskStatus::CLOSED));
        $this->assertFalse(TaskStatus::APPROVED->canTransitionTo(TaskStatus::REVIEW));
    }

    public function test_closed_cannot_transition(): void
    {
        $this->assertFalse(TaskStatus::CLOSED->canTransitionTo(TaskStatus::DRAFT));
        $this->assertFalse(TaskStatus::CLOSED->canTransitionTo(TaskStatus::ASSIGNED));
        $this->assertFalse(TaskStatus::CLOSED->canTransitionTo(TaskStatus::IN_PROGRESS));
    }

    public function test_get_next_statuses(): void
    {
        $this->assertEquals([TaskStatus::ASSIGNED], TaskStatus::DRAFT->getNextStatuses());
        $this->assertEquals([TaskStatus::IN_PROGRESS], TaskStatus::ASSIGNED->getNextStatuses());
        $this->assertEquals([TaskStatus::REVIEW, TaskStatus::CLOSED], TaskStatus::IN_PROGRESS->getNextStatuses());
        $this->assertEquals([TaskStatus::APPROVED, TaskStatus::IN_PROGRESS], TaskStatus::REVIEW->getNextStatuses());
        $this->assertEquals([TaskStatus::IN_PROGRESS, TaskStatus::CLOSED], TaskStatus::APPROVED->getNextStatuses());
        $this->assertEmpty(TaskStatus::CLOSED->getNextStatuses());
    }
}