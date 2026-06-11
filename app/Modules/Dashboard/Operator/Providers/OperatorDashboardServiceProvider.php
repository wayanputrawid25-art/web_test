<?php

namespace App\Modules\Dashboard\Operator\Providers;

use App\Modules\Dashboard\Operator\Services\OperatorDashboardService;
use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use Illuminate\Support\ServiceProvider;

class OperatorDashboardServiceProvider extends ServiceProvider
{
    protected string $name = 'OperatorDashboard';
    protected string $nameLower = 'operator_dashboard';

    public function register(): void
    {
        $this->app->singleton(OperatorDashboardService::class, function ($app) {
            return new OperatorDashboardService(
                $app->make(TaskService::class),
                $app->make(StockOpnameService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'dashboard');
    }
}