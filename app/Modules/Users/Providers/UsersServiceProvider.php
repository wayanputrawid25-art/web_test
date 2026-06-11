<?php

namespace App\Modules\Users\Providers;

use App\Modules\Users\Domain\Contracts\UserRepositoryInterface;
use App\Modules\Users\Infrastructure\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    protected string $name = 'Users';
    protected string $nameLower = 'users';

    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'users');
    }
}