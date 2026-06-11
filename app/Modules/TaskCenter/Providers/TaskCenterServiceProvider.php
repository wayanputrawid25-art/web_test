<?php

namespace App\Modules\TaskCenter\Providers;

use App\Modules\TaskCenter\Domain\Contracts\TaskRepositoryInterface;
use App\Modules\TaskCenter\Infrastructure\Repositories\TaskRepository;
use Illuminate\Support\ServiceProvider;

class TaskCenterServiceProvider extends ServiceProvider
{
    protected string $name = 'TaskCenter';
    protected string $nameLower = 'task_center';

    public function register(): void
    {
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'task_center');
    }
}