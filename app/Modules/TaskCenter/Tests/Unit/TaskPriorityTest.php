<?php

namespace App\Modules\TaskCenter\Tests\Unit;

use App\Modules\TaskCenter\Domain\ValueObjects\TaskPriority;
use PHPUnit\Framework\TestCase;

class TaskPriorityTest extends TestCase
{
    public function test_all_priorities_have_correct_values(): void
    {
        $this->assertEquals('low', TaskPriority::LOW->value);
        $this->assertEquals('medium', TaskPriority::MEDIUM->value);
        $this->assertEquals('high', TaskPriority::HIGH->value);
        $this->assertEquals('urgent', TaskPriority::URGENT->value);
    }

    public function test_labels_are_correct(): void
    {
        $this->assertEquals('Low', TaskPriority::LOW->label());
        $this->assertEquals('Medium', TaskPriority::MEDIUM->label());
        $this->assertEquals('High', TaskPriority::HIGH->label());
        $this->assertEquals('Urgent', TaskPriority::URGENT->label());
    }

    public function test_colors_are_assigned(): void
    {
        $this->assertEquals('slate', TaskPriority::LOW->color());
        $this->assertEquals('blue', TaskPriority::MEDIUM->color());
        $this->assertEquals('orange', TaskPriority::HIGH->color());
        $this->assertEquals('red', TaskPriority::URGENT->color());
    }

    public function test_can_create_from_string(): void
    {
        $priority = TaskPriority::from('high');
        $this->assertEquals(TaskPriority::HIGH, $priority);
    }
}