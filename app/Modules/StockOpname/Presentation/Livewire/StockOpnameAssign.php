<?php

namespace App\Modules\StockOpname\Presentation\Livewire;

use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\StockOpname\Domain\Entities\StockOpnameSession;
use App\Modules\Users\Infrastructure\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class StockOpnameAssign extends Component
{
    public ?StockOpnameSession $session = null;
    public array $selectedUsers = [];
    public array $currentAssignments = [];

    public function mount(int $id): void
    {
        Gate::authorize('edit-stock-opnames');

        $this->loadSession($id);
    }

    public function render()
    {
        $availableUsers = User::active()
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]);

        return view('stock_opname::assign', [
            'availableUsers' => $availableUsers,
        ]);
    }

    public function assign(): void
    {
        Gate::authorize('edit-stock-opnames');

        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Select at least one user');
            return;
        }

        try {
            app(StockOpnameService::class)->assignCounters(
                $this->session->id,
                $this->selectedUsers
            );

            session()->flash('success', 'Counters assigned successfully');
            $this->redirectRoute('stock_opnames.show', $this->session->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeAssignment(int $assignmentId): void
    {
        Gate::authorize('edit-stock-opnames');

        try {
            app(StockOpnameService::class)
                ->getAssignments($this->session->id)
                ->firstWhere('id', $assignmentId);

            // Delete assignment through repository
            \App\Modules\StockOpname\Infrastructure\Models\StockOpnameAssignment::find($assignmentId)?->delete();

            $this->loadSession($this->session->id);
            session()->flash('success', 'Assignment removed');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function toggleUser(int $userId): void
    {
        $key = array_search($userId, $this->selectedUsers);
        if ($key !== false) {
            unset($this->selectedUsers[$key]);
            $this->selectedUsers = array_values($this->selectedUsers);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    private function loadSession(int $id): void
    {
        $this->session = app(StockOpnameService::class)->getSession($id);
        $this->currentAssignments = app(StockOpnameService::class)
            ->getAssignments($id)
            ->toArray();
    }

    public function back(): void
    {
        $this->redirectRoute('stock_opnames.show', $this->session->id);
    }
}