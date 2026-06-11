<?php

namespace App\Modules\Users\Application\Services;

use App\Modules\Users\Application\Actions\UserAction;
use App\Modules\Users\Application\DTOs\CreateUserDTO;
use App\Modules\Users\Application\DTOs\UpdateUserDTO;
use App\Modules\Users\Application\DTOs\UserFilterDTO;
use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Exceptions\DuplicateEmailException;
use App\Modules\Users\Exceptions\UserNotFoundException;

class UserService
{
    public function __construct(
        private readonly UserAction $action
    ) {}

    public function getUsers(UserFilterDTO $filter): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->action->getAll($filter->toArray());
    }

    public function getUser(int $id): User
    {
        return $this->action->getById($id);
    }

    public function createUser(array $data): User
    {
        $dto = CreateUserDTO::fromArray($data);

        return $this->action->create($dto);
    }

    public function updateUser(int $id, array $data): User
    {
        $dto = UpdateUserDTO::fromArray($data);

        return $this->action->update($id, $dto);
    }

    public function deleteUser(int $id): bool
    {
        return $this->action->delete($id);
    }

    public function assignRoles(int $userId, array $roles): User
    {
        return $this->action->assignRoles($userId, $roles);
    }

    public function changeRole(int $userId, string $role): User
    {
        return $this->action->changeRole($userId, $role);
    }

    public function activateUser(int $userId): User
    {
        return $this->action->activate($userId);
    }

    public function deactivateUser(int $userId): User
    {
        return $this->action->deactivate($userId);
    }

    public function toggleUserStatus(int $userId): User
    {
        return $this->action->toggleStatus($userId);
    }

    public function getAllRoles(): array
    {
        return $this->action->getAllRoles();
    }
}