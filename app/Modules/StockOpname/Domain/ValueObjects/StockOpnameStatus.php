<?php

namespace App\Modules\StockOpname\Domain\ValueObjects;

enum StockOpnameStatus: string
{
    case CREATED = 'created';
    case ASSIGNED = 'assigned';
    case COUNTING = 'counting';
    case SUBMITTED = 'submitted';
    case REVIEW = 'review';
    case APPROVED = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::ASSIGNED => 'Assigned',
            self::COUNTING => 'Counting',
            self::SUBMITTED => 'Submitted',
            self::REVIEW => 'Review',
            self::APPROVED => 'Approved',
        };
    }

    public function color(): string
    {
        return match ($self) {
            self::CREATED => 'gray',
            self::ASSIGNED => 'blue',
            self::COUNTING => 'yellow',
            self::SUBMITTED => 'purple',
            self::REVIEW => 'orange',
            self::APPROVED => 'green',
        };
    }

    public function canTransitionTo(StockOpnameStatus $newStatus): bool
    {
        return match ($this) {
            self::CREATED => $newStatus === self::ASSIGNED,
            self::ASSIGNED => $newStatus === self::COUNTING,
            self::COUNTING => $newStatus === self::SUBMITTED || $newStatus === self::ASSIGNED,
            self::SUBMITTED => $newStatus === self::REVIEW || $newStatus === self::COUNTING,
            self::REVIEW => $newStatus === self::APPROVED || $newStatus === self::COUNTING,
            self::APPROVED => false,
        };
    }

    public function getNextStatuses(): array
    {
        return match ($this) {
            self::CREATED => [self::ASSIGNED],
            self::ASSIGNED => [self::COUNTING],
            self::COUNTING => [self::SUBMITTED, self::ASSIGNED],
            self::SUBMITTED => [self::REVIEW, self::COUNTING],
            self::REVIEW => [self::APPROVED, self::COUNTING],
            self::APPROVED => [],
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::CREATED, self::ASSIGNED, self::COUNTING]);
    }

    public function isCountingAllowed(): bool
    {
        return in_array($this, [self::ASSIGNED, self::COUNTING]);
    }

    public function isReviewable(): bool
    {
        return in_array($this, [self::SUBMITTED, self::REVIEW]);
    }
}