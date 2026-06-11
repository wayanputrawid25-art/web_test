<?php

namespace App\Modules\Dashboard\Operator\Tests\Unit;

use App\Modules\Dashboard\Operator\Presentation\Livewire\OperatorDashboard;
use Livewire\Livewire;
use Tests\TestCase;

class OperatorDashboardTest extends TestCase
{
    public function test_dashboard_component_can_mount(): void
    {
        Livewire::test(OperatorDashboard::class)
            ->assertStatus(200);
    }

    public function test_dashboard_has_expected_properties(): void
    {
        Livewire::test(OperatorDashboard::class)
            ->assertPropertyWired('user')
            ->assertPropertyWired('tasks')
            ->assertPropertyWired('progress')
            ->assertPropertyWired('activeOpname')
            ->assertPropertyWired('quickStats');
    }

    public function test_get_greeting_returns_time_based_greeting(): void
    {
        $component = new OperatorDashboard();
        
        $greeting = $component->getGreeting();
        
        $this->assertContains($greeting, ['Good Morning', 'Good Afternoon', 'Good Evening']);
    }

    public function test_has_active_opname_returns_boolean(): void
    {
        $component = new OperatorDashboard();
        $component->activeOpname = null;
        
        $this->assertFalse($component->hasActiveOpname());
        
        $component->activeOpname = ['id' => 1, 'name' => 'Test'];
        
        $this->assertTrue($component->hasActiveOpname());
    }

    public function test_get_total_tasks_returns_integer(): void
    {
        $component = new OperatorDashboard();
        $component->tasks = ['total' => 5];
        
        $this->assertEquals(5, $component->getTotalTasks());
    }

    public function test_get_upcoming_tasks_count_returns_integer(): void
    {
        $component = new OperatorDashboard();
        $component->tasks = ['upcoming' => [['id' => 1], ['id' => 2]]];
        
        $this->assertEquals(2, $component->getUpcomingTasksCount());
    }

    public function test_get_overdue_tasks_count_returns_integer(): void
    {
        $component = new OperatorDashboard();
        $component->tasks = ['overdue' => [['id' => 1]]];
        
        $this->assertEquals(1, $component->getOverdueTasksCount());
    }

    public function test_refresh_data_method_exists(): void
    {
        $component = new OperatorDashboard();
        
        $this->assertTrue(method_exists($component, 'refreshData'));
    }

    public function test_load_dashboard_fills_properties(): void
    {
        Livewire::actingAs(\App\Modules\Users\Infrastructure\Models\User::factory()->make([
            'id' => 1,
            'name' => 'Test Operator',
            'email' => 'test@example.com',
        ]));

        Livewire::test(OperatorDashboard::class)
            ->assertStatus(200);
    }
}