<?php

namespace App\Modules\StockOpname\Tests\Unit;

use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use PHPUnit\Framework\TestCase;

class StockOpnameStatusTest extends TestCase
{
    public function test_all_statuses_have_correct_values(): void
    {
        $this->assertEquals('created', StockOpnameStatus::CREATED->value);
        $this->assertEquals('assigned', StockOpnameStatus::ASSIGNED->value);
        $this->assertEquals('counting', StockOpnameStatus::COUNTING->value);
        $this->assertEquals('submitted', StockOpnameStatus::SUBMITTED->value);
        $this->assertEquals('review', StockOpnameStatus::REVIEW->value);
        $this->assertEquals('approved', StockOpnameStatus::APPROVED->value);
    }

    public function test_labels_are_correct(): void
    {
        $this->assertEquals('Created', StockOpnameStatus::CREATED->label());
        $this->assertEquals('Assigned', StockOpnameStatus::ASSIGNED->label());
        $this->assertEquals('Counting', StockOpnameStatus::COUNTING->label());
        $this->assertEquals('Submitted', StockOpnameStatus::SUBMITTED->label());
        $this->assertEquals('Review', StockOpnameStatus::REVIEW->label());
        $this->assertEquals('Approved', StockOpnameStatus::APPROVED->label());
    }

    public function test_transition_created_to_assigned(): void
    {
        $this->assertTrue(StockOpnameStatus::CREATED->canTransitionTo(StockOpnameStatus::ASSIGNED));
        $this->assertFalse(StockOpnameStatus::CREATED->canTransitionTo(StockOpnameStatus::COUNTING));
        $this->assertFalse(StockOpnameStatus::CREATED->canTransitionTo(StockOpnameStatus::APPROVED));
    }

    public function test_transition_assigned_to_counting(): void
    {
        $this->assertTrue(StockOpnameStatus::ASSIGNED->canTransitionTo(StockOpnameStatus::COUNTING));
        $this->assertFalse(StockOpnameStatus::ASSIGNED->canTransitionTo(StockOpnameStatus::SUBMITTED));
    }

    public function test_transition_counting_to_submitted_or_assigned(): void
    {
        $this->assertTrue(StockOpnameStatus::COUNTING->canTransitionTo(StockOpnameStatus::SUBMITTED));
        $this->assertTrue(StockOpnameStatus::COUNTING->canTransitionTo(StockOpnameStatus::ASSIGNED));
        $this->assertFalse(StockOpnameStatus::COUNTING->canTransitionTo(StockOpnameStatus::APPROVED));
    }

    public function test_transition_submitted_to_review_or_counting(): void
    {
        $this->assertTrue(StockOpnameStatus::SUBMITTED->canTransitionTo(StockOpnameStatus::REVIEW));
        $this->assertTrue(StockOpnameStatus::SUBMITTED->canTransitionTo(StockOpnameStatus::COUNTING));
        $this->assertFalse(StockOpnameStatus::SUBMITTED->canTransitionTo(StockOpnameStatus::APPROVED));
    }

    public function test_transition_review_to_approved_or_counting(): void
    {
        $this->assertTrue(StockOpnameStatus::REVIEW->canTransitionTo(StockOpnameStatus::APPROVED));
        $this->assertTrue(StockOpnameStatus::REVIEW->canTransitionTo(StockOpnameStatus::COUNTING));
    }

    public function test_approved_cannot_transition(): void
    {
        $this->assertFalse(StockOpnameStatus::APPROVED->canTransitionTo(StockOpnameStatus::CREATED));
        $this->assertFalse(StockOpnameStatus::APPROVED->canTransitionTo(StockOpnameStatus::COUNTING));
    }

    public function test_is_editable(): void
    {
        $this->assertTrue(StockOpnameStatus::CREATED->isEditable());
        $this->assertTrue(StockOpnameStatus::ASSIGNED->isEditable());
        $this->assertTrue(StockOpnameStatus::COUNTING->isEditable());
        $this->assertFalse(StockOpnameStatus::SUBMITTED->isEditable());
        $this->assertFalse(StockOpnameStatus::REVIEW->isEditable());
        $this->assertFalse(StockOpnameStatus::APPROVED->isEditable());
    }

    public function test_is_counting_allowed(): void
    {
        $this->assertTrue(StockOpnameStatus::ASSIGNED->isCountingAllowed());
        $this->assertTrue(StockOpnameStatus::COUNTING->isCountingAllowed());
        $this->assertFalse(StockOpnameStatus::CREATED->isCountingAllowed());
        $this->assertFalse(StockOpnameStatus::SUBMITTED->isCountingAllowed());
    }

    public function test_is_reviewable(): void
    {
        $this->assertTrue(StockOpnameStatus::SUBMITTED->isReviewable());
        $this->assertTrue(StockOpnameStatus::REVIEW->isReviewable());
        $this->assertFalse(StockOpnameStatus::COUNTING->isReviewable());
        $this->assertFalse(StockOpnameStatus::APPROVED->isReviewable());
    }

    public function test_get_next_statuses(): void
    {
        $this->assertEquals([StockOpnameStatus::ASSIGNED], StockOpnameStatus::CREATED->getNextStatuses());
        $this->assertEquals([StockOpnameStatus::COUNTING], StockOpnameStatus::ASSIGNED->getNextStatuses());
        $this->assertEquals([StockOpnameStatus::SUBMITTED, StockOpnameStatus::ASSIGNED], StockOpnameStatus::COUNTING->getNextStatuses());
        $this->assertEquals([StockOpnameStatus::REVIEW, StockOpnameStatus::COUNTING], StockOpnameStatus::SUBMITTED->getNextStatuses());
        $this->assertEquals([StockOpnameStatus::APPROVED, StockOpnameStatus::COUNTING], StockOpnameStatus::REVIEW->getNextStatuses());
        $this->assertEmpty(StockOpnameStatus::APPROVED->getNextStatuses());
    }
}