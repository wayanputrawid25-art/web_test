<?php

namespace App\Modules\Approval\Providers;

use App\Modules\Approval\Domain\Contracts\ApprovalRepositoryInterface;
use App\Modules\Approval\Infrastructure\Repositories\ApprovalRepository;
use Illuminate\Support\ServiceProvider;

class ApprovalServiceProvider extends ServiceProvider
{
    protected string $name = 'Approval';
    protected string $nameLower = 'approval';

    public function register(): void
    {
        $this->app->bind(
            ApprovalRepositoryInterface::class,
            ApprovalRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'approval');
    }
}