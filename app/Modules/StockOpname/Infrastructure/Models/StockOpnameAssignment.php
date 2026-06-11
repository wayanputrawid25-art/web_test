<?php

namespace App\Modules\StockOpname\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameAssignment extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_assignments';

    protected $fillable = [
        'stock_opname_session_id',
        'user_id',
        'assigned_by',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'stock_opname_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class, 'assigned_by');
    }
}