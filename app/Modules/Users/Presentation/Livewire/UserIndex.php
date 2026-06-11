<?php

namespace App\Modules\Users\Presentation\Livewire;

use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Application\DTOs\UserFilterDTO;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public ?string $statusFilter = null;
    public ?string $roleFilter = null;
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => null],
        'roleFilter' => ['except' => null],
        'perPage' => ['except' => 15],
    ];

    public function mount(): void
    {
        Gate::authorize('view-users');
    }

    public function render()
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
            'status' => $this->statusFilter,
            'role' => $this->roleFilter,
        ]);

        $users = app(UserService::class)->getUsers(
            UserFilterDTO::fromArray([
                ...$filters,
                'per_page' => $this->perPage,
            ])
        );

        $roles = app(UserService::class)->getAllRoles();

        return view('users::index', [
            'users' => $users,
            'roles' => $roles,
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'roleFilter']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-users');

        app(UserService::class)->deleteUser($id);

        $this->dispatch('user-deleted');
        session()->flash('success', 'User berhasil dihapus');
    }

    public function toggleStatus(int $id): void
    {
        Gate::authorize('edit-users');

        app(UserService::class)->toggleUserStatus($id);

        $this->dispatch('user-updated');
        session()->flash('success', 'Status user berhasil diubah');
    }
}