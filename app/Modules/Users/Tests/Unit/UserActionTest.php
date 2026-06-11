<?php

namespace App\Modules\Users\Tests\Unit;

use App\Modules\Users\Domain\Contracts\UserRepositoryInterface;
use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use App\Modules\Users\Application\Actions\UserAction;
use App\Modules\Users\Application\DTOs\CreateUserDTO;
use App\Modules\Users\Exceptions\DuplicateEmailException;
use App\Modules\Users\Exceptions\UserNotFoundException;
use PHPUnit\Framework\TestCase;
use Mockery;

class UserActionTest extends TestCase
{
    private $repository;
    private UserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->action = new UserAction($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_by_id_returns_user(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $result = $this->action->getById(1);

        $this->assertEquals($user, $result);
    }

    public function test_get_by_id_throws_not_found_exception(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->action->getById(999);
    }

    public function test_create_user_successfully(): void
    {
        $dto = new CreateUserDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            status: 'active',
            roles: ['SuperAdmin']
        );

        $createdUser = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE,
            roles: ['SuperAdmin']
        );

        $this->repository
            ->shouldReceive('existsByEmail')
            ->with('john@example.com')
            ->once()
            ->andReturn(false);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdUser);

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($createdUser);

        $result = $this->action->create($dto);

        $this->assertEquals($createdUser, $result);
    }

    public function test_create_user_throws_duplicate_email_exception(): void
    {
        $dto = new CreateUserDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            status: 'active'
        );

        $this->repository
            ->shouldReceive('existsByEmail')
            ->with('john@example.com')
            ->once()
            ->andReturn(true);

        $this->expectException(DuplicateEmailException::class);

        $this->action->create($dto);
    }

    public function test_delete_user_successfully(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->action->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_user_throws_not_found_exception(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->action->delete(999);
    }

    public function test_activate_user(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::INACTIVE
        );

        $activeUser = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('update')
            ->with(1, ['status' => 'active'])
            ->once()
            ->andReturn($activeUser);

        $result = $this->action->activate(1);

        $this->assertEquals(UserStatus::ACTIVE, $result->status);
    }

    public function test_deactivate_user(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE
        );

        $inactiveUser = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::INACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('update')
            ->with(1, ['status' => 'inactive'])
            ->once()
            ->andReturn($inactiveUser);

        $result = $this->action->deactivate(1);

        $this->assertEquals(UserStatus::INACTIVE, $result->status);
    }

    public function test_toggle_status(): void
    {
        $user = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::ACTIVE
        );

        $inactiveUser = new User(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            status: UserStatus::INACTIVE
        );

        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('update')
            ->with(1, ['status' => 'inactive'])
            ->once()
            ->andReturn($inactiveUser);

        $result = $this->action->toggleStatus(1);

        $this->assertEquals(UserStatus::INACTIVE, $result->status);
    }
}