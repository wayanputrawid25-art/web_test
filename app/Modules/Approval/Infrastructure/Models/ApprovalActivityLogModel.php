<?php

namespace App\Modules\Approval\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalActivityLog extends Model
{
    use HasFactory;

    protected $table = 'approval_activity_logs';

    protected $fillable = [
        'approval_request_id',
        'action',
        'old_value',
        'new_value',
        'user_id',
        'notes',
    ];

    // Relationships
    public function request(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class, 'approval_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class);
    }
}