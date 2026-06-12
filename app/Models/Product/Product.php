<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Supplier\Supplier;
use App\Models\StockIn\PurchaseOrderItem;
use App\Models\StockOut\StockOutItem;
use App\Modules\Inventory\Infrastructure\Models\InventoryTransaction;
use App\Modules\Inventory\Infrastructure\Models\StockLedger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'supplier_id',
        'unit',
        'cost_price',
        'selling_price',
        'min_stock',
        'max_stock',
        'current_stock',
        'description',
        'barcode',
        'image',
        'status',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'current_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function stockLedger(): HasMany
    {
        return $this->hasMany(StockLedger::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeLowStock($query)
    {
        return $query->where('current_stock', '<=', 'min_stock');
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('sku', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%")
              ->orWhere('barcode', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%")
              ->orWhereHas('category', fn ($cq) => $cq->where('name', 'ilike', "%{$search}%"))
              ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'ilike', "%{$search}%"));
        });
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        }
        if ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'normal';
    }
}