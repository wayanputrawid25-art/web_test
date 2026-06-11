<?php

namespace App\Modules\TaskCenter\Infrastructure\Models;

use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use App\Modules\TaskCenter\Domain\ValueObjects\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'assignee_id',
        'creator_id',
        'product_id',
        'inventory_transaction_id',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
        ];
    }

    // Relationships
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'creator_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Product\Infrastructure\Models\Product::class, 'product_id');
    }

    public function inventoryTransaction(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Inventory\Infrastructure\Models\InventoryTransaction::class, 'inventory_transaction_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignee($query, int $assigneeId)
    {
        return $query->where('assignee_id', $assigneeId);
    }

    public function scopeCreator($query, int $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
        });
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['closed']);
    }
}