<?php

namespace App\Modules\Dashboard\Operator\Presentation\Livewire;

use App\Modules\Dashboard\Operator\Services\OperatorDashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class OperatorDashboard extends Component
{
    public array $user = [];
    public array $tasks = [];
    public array $progress = [];
    public ?array $activeOpname = null;
    public array $quickStats = [];
    public bool $isLoading = true;

    public function mount(): void
    {
        $this->loadDashboard();
    }

    public function render()
    {
        return view('dashboard::operator.index');
    }

    public function refreshData(): void
    {
        $this->loadDashboard();
    }

    private function loadDashboard(): void
    {
        $userId = auth()->id();

        $service = app(OperatorDashboardService::class);

        $data = $service->getOperatorData($userId);

        $this->user = $data['user'] ?? [];
        $this->tasks = $data['tasks'];
        $this->progress = $data['progress'];
        $this->activeOpname = $data['activeOpname'];

        $this->quickStats = $service->getQuickStats($userId);

        $this->isLoading = false;
    }

    public function getTotalTasks(): int
    {
        return $this->tasks['total'] ?? 0;
    }

    public function getUpcomingTasksCount(): int
    {
        return count($this->tasks['upcoming'] ?? []);
    }

    public function getOverdueTasksCount(): int
    {
        return count($this->tasks['overdue'] ?? []);
    }

    public function hasActiveOpname(): bool
    {
        return $this->activeOpname !== null;
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
}