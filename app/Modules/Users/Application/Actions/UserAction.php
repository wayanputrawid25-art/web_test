<?php

namespace App\Modules\Users\Application\Actions;

use App\Modules\Users\Application\DTOs\CreateUserDTO;
use App\Modules\Users\Application\DTOs\UpdateUserDTO;
use App\Modules\Users\Domain\Contracts\UserRepositoryInterface;
use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use App\Modules\Users\Exceptions\DuplicateEmailException;
use App\Modules\Users\Exceptions\UserNotFoundException;
use App\Modules\Users\Infrastructure\Models\User as UserModel;
use Spatie\Permission\Models\Role;

class UserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->findAll($filters);
    }

    public function getById(int $id): User
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new UserNotFoundException($id);
        }

        return $user;
    }

    public function create(CreateUserDTO $dto): User
    {
        $data = $dto->toArray();

        if ($this->repository->existsByEmail($data['email'])) {
            throw new DuplicateEmailException($data['email']);
        }

        $user = $this->repository->create($data);

        // Assign roles if provided
        if (!empty($dto->roles)) {
            $this->assignRoles($user->id, $dto->roles);
        }

        return $this->getById($user->id);
    }

    public function update(int $id, UpdateUserDTO $dto): User
    {
        $data = $dto->toArray();

        if ($this->repository->existsByEmail($data['email'], $id)) {
            throw new DuplicateEmailException($data['email']);
        }

        $user = $this->repository->findById($id);

        if (!$user) {
            throw new UserNotFoundException($id);
        }

        $user = $this->repository->update($id, $data);

        // Update roles if provided
        if (!empty($dto->roles)) {
            $this->assignRoles($id, $dto->roles);
        }

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new UserNotFoundException($id);
        }

        return $this->repository->delete($id);
    }

    public function assignRoles(int $userId, array $roles): User
    {
        $user = UserModel::findOrFail($userId);
        
        // Validate roles
        $validRoles = Role::whereIn('name', $roles)->pluck('name')->toArray();
        
        // Remove existing roles and assign new ones
        $user->syncRoles($validRoles);

        return $this->getById($userId);
    }

    public function changeRole(int $userId, string $role): User
    {
        $user = UserModel::findOrFail($userId);
        
        // Validate role exists
        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel) {
            throw new \App\Modules\Users\Exceptions\InvalidRoleException($role);
        }

        // Replace all roles with the new one
        $user->syncRoles([$role]);

        return $this->getById($userId);
    }

    public function activate(int $userId): User
    {
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        return $this->repository->update($userId, ['status' => UserStatus::ACTIVE->value]);
    }

    public function deactivate(int $userId): User
    {
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        return $this->repository->update($userId, ['status' => UserStatus::INACTIVE->value]);
    }

    public function toggleStatus(int $userId): User
    {
        $user = $this->repository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        $newStatus = $user->isActive() ? UserStatus::INACTIVE : UserStatus::ACTIVE;

        return $this->repository->update($userId, ['status' => $newStatus->value]);
    }

    public function getAllRoles(): array
    {
        return Role::orderBy('name')->pluck('name')->toArray();
    }
}