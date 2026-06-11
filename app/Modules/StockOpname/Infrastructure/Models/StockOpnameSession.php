<?php

namespace App\Modules\StockOpname\Infrastructure\Models;

use App\Modules\StockOpname\Domain\ValueObjects\StockOpnameStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpnameSession extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_sessions';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'creator_id',
        'task_id',
        'start_date',
        'end_date',
        'count_deadline',
    ];

    protected function casts(): array
    {
        return [
            'status' => StockOpnameStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'count_deadline' => 'date',
        ];
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'creator_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\TaskCenter\Infrastructure\Models\Task::class, 'task_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_session_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(StockOpnameAssignment::class, 'stock_opname_session_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(StockOpnameActivityLog::class, 'stock_opname_session_id')->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%");
        });
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['approved']);
    }
}