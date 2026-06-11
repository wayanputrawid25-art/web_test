<?php

namespace App\Modules\StockOpname\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameActivityLog extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_activity_logs';

    protected $fillable = [
        'stock_opname_session_id',
        'action',
        'old_value',
        'new_value',
        'user_id',
        'notes',
    ];

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'stock_opname_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Infrastructure\Models\User::class);
    }
}