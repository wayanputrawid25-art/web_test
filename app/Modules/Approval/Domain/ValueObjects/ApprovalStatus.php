<?php

namespace App\Modules\Approval\Domain\ValueObjects;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVISION_REQUESTED = 'revision_requested';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::REVISION_REQUESTED => 'Revision Requested',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::REVISION_REQUESTED => 'orange',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    public function canBeActionedBy(int $userId, int $requesterId): bool
    {
        // Cannot approve/reject your own request
        if ($userId === $requesterId) {
            return false;
        }
        return $this === self::PENDING;
    }
}