<?php

namespace App\Modules\StockOpname\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_items';

    protected $fillable = [
        'stock_opname_session_id',
        'product_id',
        'system_quantity',
        'counted_quantity',
        'variance',
        'notes',
        'counter_id',
        'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'decimal:2',
            'counted_quantity' => 'decimal:2',
            'variance' => 'decimal:2',
            'counted_at' => 'datetime',
        ];
    }

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'stock_opname_session_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Product\Infrastructure\Models\Product::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'counter_id');
    }

    // Accessors
    public function getHasVarianceAttribute(): bool
    {
        return $this->variance !== null && $this->variance != 0;
    }

    public function getIsCountedAttribute(): bool
    {
        return $this->counted_quantity !== null;
    }

    // Mutators
    public function setCountedQuantityAttribute($value): void
    {
        $this->attributes['counted_quantity'] = $value;
        $this->attributes['variance'] = $value !== null 
            ? round((float)$value - (float)$this->attributes['system_quantity'], 2)
            : null;
    }
}