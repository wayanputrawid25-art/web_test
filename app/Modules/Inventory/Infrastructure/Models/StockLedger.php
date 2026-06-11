<?php

namespace App\Modules\Inventory\Infrastructure\Models;

use App\Modules\Product\Infrastructure\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLedger extends Model
{
    protected $table = 'stock_ledger';

    protected $fillable = [
        'product_id',
        'stock_in',
        'stock_out',
        'balance',
        'transaction_id',
        'transaction_type',
        'reference',
        'user_id',
    ];

    protected $casts = [
        'stock_in' => 'integer',
        'stock_out' => 'integer',
        'balance' => 'integer',
        'transaction_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('reference', 'ilike', "%{$search}%")
              ->orWhereHas('product', function ($pq) use ($search) {
                  $pq->where('sku', 'ilike', "%{$search}%")
                     ->orWhere('name', 'ilike', "%{$search}%");
              });
        });
    }
}