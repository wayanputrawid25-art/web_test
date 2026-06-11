<?php

namespace App\Modules\Users\Tests\Unit;

use App\Modules\Users\Domain\ValueObjects\UserStatus;
use PHPUnit\Framework\TestCase;

class UserStatusTest extends TestCase
{
    public function test_active_has_correct_value(): void
    {
        $status = UserStatus::ACTIVE;

        $this->assertEquals('active', $status->value);
        $this->assertEquals('Active', $status->label());
        $this->assertTrue($status->isActive());
    }

    public function test_inactive_has_correct_value(): void
    {
        $status = UserStatus::INACTIVE;

        $this->assertEquals('inactive', $status->value);
        $this->assertEquals('Inactive', $status->label());
        $this->assertFalse($status->isActive());
    }

    public function test_can_create_from_string(): void
    {
        $status = UserStatus::from('active');

        $this->assertEquals(UserStatus::ACTIVE, $status);
    }

    public function test_all_cases_exist(): void
    {
        $cases = UserStatus::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(UserStatus::ACTIVE, $cases);
        $this->assertContains(UserStatus::INACTIVE, $cases);
    }
}