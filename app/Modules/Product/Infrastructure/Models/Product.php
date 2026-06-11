<?php

namespace App\Modules\Product\Infrastructure\Models;

use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'category',
        'status',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::ACTIVE->value);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProductStatus::INACTIVE->value);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('sku', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%")
              ->orWhere('category', 'ilike', "%{$search}%");
        });
    }

    public function isActive(): bool
    {
        return $this->status === ProductStatus::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === ProductStatus::INACTIVE;
    }
}