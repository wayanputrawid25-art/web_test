<?php

namespace App\Modules\Users\Presentation\Livewire;

use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Domain\Entities\User;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class UserEdit extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $status = 'active';
    public array $selectedRoles = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'status' => ['required', 'in:active,inactive'],
            'selectedRoles' => ['array'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ];
    }

    public function mount(int $id): void
    {
        Gate::authorize('edit-users');

        $this->user = app(UserService::class)->getUser($id);

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->status = $this->user->status?->value ?? 'active';
        $this->selectedRoles = $this->user->roles;
    }

    public function render()
    {
        $roles = app(UserService::class)->getAllRoles();

        return view('users::edit', [
            'roles' => $roles,
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'selectedRoles' => ['array'],
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'roles' => $this->selectedRoles,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        try {
            app(UserService::class)->updateUser($this->user->id, $data);

            session()->flash('success', 'User berhasil diperbarui');

            $this->redirectRoute('users.index');
        } catch (\App\Modules\Users\Exceptions\DuplicateEmailException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->redirectRoute('users.index');
    }
}