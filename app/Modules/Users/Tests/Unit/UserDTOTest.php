<?php

namespace App\Modules\Users\Tests\Unit;

use App\Modules\Users\Application\DTOs\CreateUserDTO;
use App\Modules\Users\Application\DTOs\UpdateUserDTO;
use App\Modules\Users\Application\DTOs\UserFilterDTO;
use PHPUnit\Framework\TestCase;

class UserDTOTest extends TestCase
{
    public function test_create_user_dto_from_array(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'status' => 'active',
            'roles' => ['SuperAdmin'],
        ];

        $dto = CreateUserDTO::fromArray($data);

        $this->assertEquals('John Doe', $dto->name);
        $this->assertEquals('john@example.com', $dto->email);
        $this->assertEquals('password123', $dto->password);
        $this->assertEquals('active', $dto->status);
        $this->assertEquals(['SuperAdmin'], $dto->roles);
    }

    public function test_create_user_dto_to_array(): void
    {
        $dto = new CreateUserDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            status: 'active',
            roles: ['SuperAdmin']
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'status' => 'active',
        ], $array);
        $this->assertArrayNotHasKey('roles', $array); // roles not in toArray
    }

    public function test_update_user_dto_from_array(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'status' => 'inactive',
            'roles' => ['Operator'],
        ];

        $dto = UpdateUserDTO::fromArray($data);

        $this->assertEquals('Jane Doe', $dto->name);
        $this->assertEquals('jane@example.com', $dto->email);
        $this->assertNull($dto->password);
        $this->assertEquals('inactive', $dto->status);
        $this->assertEquals(['Operator'], $dto->roles);
    }

    public function test_update_user_dto_with_password(): void
    {
        $dto = new UpdateUserDTO(
            name: 'Jane Doe',
            email: 'jane@example.com',
            password: 'newpassword',
            status: 'active',
            roles: []
        );

        $array = $dto->toArray();

        $this->assertEquals('newpassword', $array['password']);
    }

    public function test_user_filter_dto_defaults(): void
    {
        $dto = UserFilterDTO::fromArray([]);

        $this->assertNull($dto->search);
        $this->assertNull($dto->status);
        $this->assertNull($dto->role);
        $this->assertEquals(15, $dto->perPage);
    }

    public function test_user_filter_dto_with_values(): void
    {
        $dto = UserFilterDTO::fromArray([
            'search' => 'john',
            'status' => 'active',
            'role' => 'SuperAdmin',
            'per_page' => 25,
        ]);

        $this->assertEquals('john', $dto->search);
        $this->assertEquals('active', $dto->status);
        $this->assertEquals('SuperAdmin', $dto->role);
        $this->assertEquals(25, $dto->perPage);
    }

    public function test_user_filter_dto_filters_nulls_in_to_array(): void
    {
        $dto = new UserFilterDTO(
            search: null,
            status: 'active',
            role: null,
            perPage: 20
        );

        $array = $dto->toArray();

        $this->assertArrayNotHasKey('search', $array);
        $this->assertArrayNotHasKey('role', $array);
        $this->assertEquals(['status' => 'active'], $array);
    }
}