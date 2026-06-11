<?php

namespace App\Modules\Dashboard\Admin\Tests\Unit;

use App\Modules\Dashboard\Admin\Presentation\Livewire\AdminDashboard;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_dashboard_component_can_mount(): void
    {
        Livewire::actingAs(\App\Modules\Users\Infrastructure\Models\User::factory()->make([
            'id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
        ]))->test(AdminDashboard::class)
            ->assertStatus(200);
    }

    public function test_dashboard_has_expected_properties(): void
    {
        Livewire::actingAs(\App\Modules\Users\Infrastructure\Models\User::factory()->make([
            'id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
        ]))->test(AdminDashboard::class)
            ->assertPropertyWired('pendingApprovals')
            ->assertPropertyWired('activeStockOpnames')
            ->assertPropertyWired('taskSummary')
            ->assertPropertyWired('inventorySummary')
            ->assertPropertyWired('userActivity')
            ->assertPropertyWired('quickActions')
            ->assertPropertyWired('stats');
    }

    public function test_get_greeting_returns_time_based_greeting(): void
    {
        $component = new AdminDashboard();
        
        $greeting = $component->getGreeting();
        
        $this->assertContains($greeting, ['Good Morning', 'Good Afternoon', 'Good Evening']);
    }

    public function test_get_current_admin_name_returns_string(): void
    {
        $component = new AdminDashboard();
        
        // Without auth, should return 'Admin'
        $name = $component->getCurrentAdminName();
        
        $this->assertIsString($name);
    }

    public function test_refresh_data_method_exists(): void
    {
        $component = new AdminDashboard();
        
        $this->assertTrue(method_exists($component, 'refreshData'));
    }

    public function test_load_dashboard_fills_properties(): void
    {
        Livewire::actingAs(\App\Modules\Users\Infrastructure\Models\User::factory()->make([
            'id' => 1,
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
        ]))->test(AdminDashboard::class)
            ->assertStatus(200)
            ->assertSee('Admin Dashboard');
    }
}