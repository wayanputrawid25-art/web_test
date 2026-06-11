<?php

namespace App\Modules\Users\Infrastructure\Models;

use App\Modules\Users\Domain\ValueObjects\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

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
            'status' => UserStatus::class,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::ACTIVE->value);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::INACTIVE->value);
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

    public function scopeByRole($query, string $role)
    {
        return $query->role($role);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === UserStatus::INACTIVE;
    }
}