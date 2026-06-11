<?php

namespace App\Modules\TaskCenter\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivityLog extends Model
{
    use HasFactory;

    protected $table = 'task_activity_logs';

    protected $fillable = [
        'task_id',
        'action',
        'old_value',
        'new_value',
        'user_id',
        'notes',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class);
    }
}