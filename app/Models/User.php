<?php

namespace App\Models;

use App\Models\StockIn\PurchaseOrder;
use App\Models\StockOut\StockOut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }

    public function createdPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function receivedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'received_by');
    }

    public function createdStockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'created_by');
    }

    public function approvedStockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'approved_by');
    }

    public function dispatchedStockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'dispatched_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%");
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}