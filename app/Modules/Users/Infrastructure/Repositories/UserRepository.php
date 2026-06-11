<?php

namespace App\Modules\Users\Infrastructure\Repositories;

use App\Modules\Users\Domain\Contracts\UserRepositoryInterface;
use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use App\Modules\Users\Infrastructure\Models\User as UserModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);

        return $model ? User::fromModel($model) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();

        return $model ? User::fromModel($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = UserModel::query();

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['role']) && $filters['role']) {
            $query->role($filters['role']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        $model = UserModel::create($data);

        return User::fromModel($model);
    }

    public function update(int $id, array $data): User
    {
        $model = UserModel::findOrFail($id);
        $model->update($data);
        $model->refresh();

        return User::fromModel($model);
    }

    public function delete(int $id): bool
    {
        $model = UserModel::findOrFail($id);

        return $model->delete();
    }

    public function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $query = UserModel::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function findByStatus(UserStatus $status): Collection
    {
        return UserModel::where('status', $status->value)
            ->get()
            ->map(fn ($model) => User::fromModel($model));
    }
}