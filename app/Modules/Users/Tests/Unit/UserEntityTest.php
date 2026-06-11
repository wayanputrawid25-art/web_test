<?php

namespace App\Modules\Users\Tests\Unit;

use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE,
            roles: ['SuperAdmin'],
            createdAt: new \DateTimeImmutable('2024-01-15 10:30:00'),
            updatedAt: new \DateTimeImmutable('2024-01-15 10:30:00'),
        );

        $this->assertEquals(1, $user->id);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertEquals(['SuperAdmin'], $user->roles);
        $this->assertTrue($user->isActive());
    }

    public function test_user_has_role(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE,
            roles: ['SuperAdmin', 'WarehouseAdmin'],
        );

        $this->assertTrue($user->hasRole('SuperAdmin'));
        $this->assertTrue($user->hasRole('WarehouseAdmin'));
        $this->assertFalse($user->hasRole('Operator'));
    }

    public function test_inactive_user(): void
    {
        $user = new User(
            id: 1,
            name: 'Jane Doe',
            email: 'jane@example.com',
            status: UserStatus::INACTIVE,
            roles: ['Operator'],
        );

        $this->assertFalse($user->isActive());
        $this->assertTrue($user->hasRole('Operator'));
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $createdAt = new \DateTimeImmutable('2024-01-15 10:30:00');

        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE,
            roles: ['SuperAdmin'],
            createdAt: $createdAt,
        );

        $array = $user->toArray();

        $this->assertEquals([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'active',
            'roles' => ['SuperAdmin'],
            'created_at' => '2024-01-15 10:30:00',
            'updated_at' => null,
            'email_verified_at' => null,
        ], $array);
    }
}