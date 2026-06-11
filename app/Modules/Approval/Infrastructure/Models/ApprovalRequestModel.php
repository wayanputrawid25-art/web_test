<?php

namespace App\Modules\Approval\Infrastructure\Models;

use App\Modules\Approval\Domain\ValueObjects\ApprovalStatus;
use App\Modules\Approval\Domain\ValueObjects\ApprovalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalRequest extends Model
{
    use HasFactory;

    protected $table = 'approval_requests';

    protected $fillable = [
        'code',
        'type',
        'status',
        'reference_id',
        'title',
        'description',
        'requester_id',
        'approver_id',
        'notes',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ApprovalType::class,
            'status' => ApprovalStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'approver_id');
    }

    public function decision(): HasOne
    {
        return $this->hasOne(ApprovalDecision::class, 'approval_request_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ApprovalActivityLog::class, 'approval_request_id')->orderBy('created_at', 'desc');
    }

    // Polymorphic relationship for reference
    public function reference()
    {
        return $this->morphTo('reference', 'type', 'reference_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', ApprovalStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ApprovalStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ApprovalStatus::REJECTED);
    }

    public function scopeRevisionRequested($query)
    {
        return $query->where('status', ApprovalStatus::REVISION_REQUESTED);
    }

    public function scopeOfType($query, ApprovalType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeForApprover($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('approver_id')
              ->orWhere('approver_id', $userId);
        });
    }

    public function scopeByRequester($query, int $userId)
    {
        return $query->where('requester_id', $userId);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'ilike', "%{$search}%")
              ->orWhere('title', 'ilike', "%{$search}%");
        });
    }
}