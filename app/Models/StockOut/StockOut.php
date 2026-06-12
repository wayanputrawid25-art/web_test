<?php

namespace App\Models\StockOut;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_outs';

    protected $fillable = [
        'code',
        'created_by',
        'approved_by',
        'dispatched_by',
        'request_date',
        'expected_date',
        'dispatched_date',
        'status',
        'reference_type',
        'reference_id',
        'destination',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'expected_date' => 'date',
        'dispatched_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'ilike', "%{$search}%")
              ->orWhere('destination', 'ilike', "%{$search}%")
              ->orWhere('notes', 'ilike', "%{$search}%");
        });
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'partial' => 'Partially Dispatched',
            'dispatched' => 'Dispatched',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'blue',
            'partial' => 'orange',
            'dispatched' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getTotalQuantityRequested(): int
    {
        return $this->items->sum('quantity_requested');
    }

    public function getTotalQuantityDispatched(): int
    {
        return $this->items->sum('quantity_dispatched');
    }

    public function isFullyDispatched(): bool
    {
        return $this->getTotalQuantityDispatched() >= $this->getTotalQuantityRequested();
    }
}