<?php

namespace App\Modules\Users\Presentation\Livewire;

use App\Modules\Users\Application\Services\UserService;
use App\Modules\Users\Domain\Entities\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class UserView extends Component
{
    public ?User $user = null;

    public function mount(int $id): void
    {
        Gate::authorize('view-users');

        $this->user = app(UserService::class)->getUser($id);
    }

    public function render()
    {
        return view('users::view');
    }

    public function back(): void
    {
        $this->redirectRoute('users.index');
    }
}