<?php

namespace App\Modules\Users\Domain\Contracts;

use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    
    public function findByEmail(string $email): ?User;
    
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): User;
    
    public function delete(int $id): bool;
    
    public function existsByEmail(string $email, ?int $excludeId = null): bool;
    
    public function findByStatus(UserStatus $status): Collection;
}