<?php

namespace App\Modules\Users\Presentation\Livewire;

use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Domain\ValueObjects\UserStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class UserCreate extends Component
{
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
            'password' => ['required', 'min:8', 'confirmed'],
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
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('create-users');
    }

    public function render()
    {
        $roles = app(UserService::class)->getAllRoles();

        return view('users::create', [
            'roles' => $roles,
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        try {
            app(UserService::class)->createUser([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'status' => $this->status,
                'roles' => $this->selectedRoles,
            ]);

            session()->flash('success', 'User berhasil ditambahkan');

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