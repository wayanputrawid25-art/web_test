<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleIndex extends Component
{
    public string $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->paginate(10);

        return view('livewire.role-index', compact('roles'))->layout('components.layouts.app');
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findById($id);
        
        if (in_array($role->name, ['Super Admin'])) {
            session()->flash('error', 'Cannot delete Super Admin role.');
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }
}