<?php

namespace App\Modules\Users\Domain\Entities;

use App\Modules\Users\Domain\ValueObjects\UserStatus;
use App\Modules\Users\Infrastructure\Models\User as UserModel;

class User
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?UserStatus $status,
        public readonly array $roles = [],
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
        public readonly ?\DateTimeImmutable $emailVerifiedAt = null,
    ) {}

    public static function fromModel(UserModel $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            status: $model->status ? UserStatus::from($model->status) : null,
            roles: $model->getRoleNames()->toArray(),
            createdAt: $model->created_at ? \Carbon\Carbon::parse($model->created_at)->toImmutable() : null,
            updatedAt: $model->updated_at ? \Carbon\Carbon::parse($model->updated_at)->toImmutable() : null,
            emailVerifiedAt: $model->email_verified_at ? \Carbon\Carbon::parse($model->email_verified_at)->toImmutable() : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status?->value,
            'roles' => $this->roles,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'email_verified_at' => $this->emailVerifiedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function isActive(): bool
    {
        return $this->status?->isActive() ?? false;
    }
}