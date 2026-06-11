<?php

namespace App\Modules\Inventory\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Inventory\Domain\Contracts\InventoryRepositoryInterface;
use App\Modules\Inventory\Infrastructure\Repositories\InventoryRepository;

class InventoryServiceProvider extends ServiceProvider
{
    protected string $name = 'Inventory';
    protected string $nameLower = 'inventory';

    public function register(): void
    {
        $this->app->bind(
            InventoryRepositoryInterface::class,
            InventoryRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'inventory');
    }
}