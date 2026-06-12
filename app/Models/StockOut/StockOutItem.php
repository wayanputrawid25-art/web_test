<?php

namespace App\Models\StockOut;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutItem extends Model
{
    protected $table = 'stock_out_items';

    protected $fillable = [
        'stock_out_id',
        'product_id',
        'quantity_requested',
        'quantity_dispatched',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_dispatched' => 'integer',
    ];

    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(StockOut::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getQuantityPendingAttribute(): int
    {
        return $this->quantity_requested - $this->quantity_dispatched;
    }

    public function isFullyDispatched(): bool
    {
        return $this->quantity_dispatched >= $this->quantity_requested;
    }
}