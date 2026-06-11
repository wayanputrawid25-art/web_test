<?php

namespace App\Modules\TaskCenter\Domain\ValueObjects;

enum TaskStatus: string
{
    case DRAFT = 'draft';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case APPROVED = 'approved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ASSIGNED => 'Assigned',
            self::IN_PROGRESS => 'In Progress',
            self::REVIEW => 'Review',
            self::APPROVED => 'Approved',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ASSIGNED => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::REVIEW => 'purple',
            self::APPROVED => 'green',
            self::CLOSED => 'slate',
        };
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function isAssigned(): bool
    {
        return $this === self::ASSIGNED;
    }

    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isReview(): bool
    {
        return $this === self::REVIEW;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isClosed(): bool
    {
        return $this === self::CLOSED;
    }

    public function canTransitionTo(TaskStatus $newStatus): bool
    {
        return match ($this) {
            self::DRAFT => $newStatus === self::ASSIGNED,
            self::ASSIGNED => $newStatus === self::IN_PROGRESS,
            self::IN_PROGRESS => $newStatus === self::REVIEW || $newStatus === self::CLOSED,
            self::REVIEW => $newStatus === self::APPROVED || $newStatus === self::IN_PROGRESS,
            self::APPROVED => $newStatus === self::IN_PROGRESS || $newStatus === self::CLOSED,
            self::CLOSED => false,
        };
    }

    public function getNextStatuses(): array
    {
        return match ($this) {
            self::DRAFT => [self::ASSIGNED],
            self::ASSIGNED => [self::IN_PROGRESS],
            self::IN_PROGRESS => [self::REVIEW, self::CLOSED],
            self::REVIEW => [self::APPROVED, self::IN_PROGRESS],
            self::APPROVED => [self::IN_PROGRESS, self::CLOSED],
            self::CLOSED => [],
        };
    }
}