<?php

namespace App\Modules\StockOpname\Providers;

use App\Modules\StockOpname\Domain\Contracts\StockOpnameRepositoryInterface;
use App\Modules\StockOpname\Infrastructure\Repositories\StockOpnameRepository;
use Illuminate\Support\ServiceProvider;

class StockOpnameServiceProvider extends ServiceProvider
{
    protected string $name = 'StockOpname';
    protected string $nameLower = 'stock_opname';

    public function register(): void
    {
        $this->app->bind(
            StockOpnameRepositoryInterface::class,
            StockOpnameRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'stock_opname');
    }
}