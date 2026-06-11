<?php

namespace App\Modules\Users\Presentation\Livewire;

use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Domain\Entities\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class UserRole extends Component
{
    public ?User $user = null;
    public string $selectedRole = '';

    protected function rules(): array
    {
        return [
            'selectedRole' => ['required', 'string'],
        ];
    }

    public function mount(int $id): void
    {
        Gate::authorize('edit-users');

        $this->user = app(UserService::class)->getUser($id);

        // Get current primary role (first role)
        $currentRoles = $this->user->roles;
        $this->selectedRole = $currentRoles[0] ?? '';
    }

    public function render()
    {
        $roles = app(UserService::class)->getAllRoles();
        $allPermissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();

        return view('users::roles', [
            'roles' => $roles,
            'allPermissions' => $allPermissions,
        ]);
    }

    public function save(): void
    {
        $this->validate();

        try {
            app(UserService::class)->changeRole($this->user->id, $this->selectedRole);

            $this->user = app(UserService::class)->getUser($this->user->id);

            session()->flash('success', 'Role berhasil diubah');
        } catch (\App\Modules\Users\Exceptions\InvalidRoleException $e) {
            $this->addError('selectedRole', $e->getMessage());
        }
    }

    public function assignRole(string $role): void
    {
        try {
            app(UserService::class)->assignRoles($this->user->id, array_merge($this->user->roles, [$role]));

            $this->user = app(UserService::class)->getUser($this->user->id);

            session()->flash('success', "Role {$role} berhasil ditambahkan");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeRole(string $role): void
    {
        $newRoles = array_filter($this->user->roles, fn ($r) => $r !== $role);

        if (empty($newRoles)) {
            session()->flash('error', 'User harus memiliki minimal satu role');
            return;
        }

        try {
            app(UserService::class)->assignRoles($this->user->id, array_values($newRoles));

            $this->user = app(UserService::class)->getUser($this->user->id);

            session()->flash('success', "Role {$role} berhasil dihapus");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirectRoute('users.index');
    }
}