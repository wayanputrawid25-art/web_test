<?php

namespace App\Modules\Dashboard\Admin\Presentation\Livewire;

use App\Modules\Dashboard\Admin\Services\AdminDashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class AdminDashboard extends Component
{
    public array $pendingApprovals = [];
    public array $activeStockOpnames = [];
    public array $taskSummary = [];
    public array $inventorySummary = [];
    public array $userActivity = [];
    public array $quickActions = [];
    public array $stats = [];
    public bool $isLoading = true;

    public function mount(): void
    {
        Gate::authorize('access-admin-dashboard');
        $this->loadDashboard();
    }

    public function render()
    {
        return view('dashboard::admin.index');
    }

    public function refreshData(): void
    {
        $this->loadDashboard();
    }

    private function loadDashboard(): void
    {
        $service = app(AdminDashboardService::class);

        $this->pendingApprovals = $service->getPendingApprovals();
        $this->activeStockOpnames = $service->getActiveStockOpnames();
        $this->taskSummary = $service->getTaskSummary();
        $this->inventorySummary = $service->getInventorySummary();
        $this->userActivity = $service->getUserActivity();
        $this->quickActions = $service->getQuickActions();
        $this->stats = $service->getStats();

        $this->isLoading = false;
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Good Morning';
        } elseif ($hour < 17) {
            return 'Good Afternoon';
        } else {
            return 'Good Evening';
        }
    }

    public function getCurrentAdminName(): string
    {
        return auth()->user()?->name ?? 'Admin';
    }
}