<?php

namespace App\Modules\Dashboard\Admin\Providers;

use App\Modules\Dashboard\Admin\Services\AdminDashboardService;
use App\Modules\Approval\Application\Services\ApprovalService;
use App\Modules\StockOpname\Application\Services\StockOpnameService;
use App\Modules\TaskCenter\Application\Services\TaskService;
use App\Modules\Inventory\Application\Services\InventoryService;
use App\Modules\Product\Application\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AdminDashboardServiceProvider extends ServiceProvider
{
    protected string $name = 'AdminDashboard';
    protected string $nameLower = 'admin_dashboard';

    public function register(): void
    {
        $this->app->singleton(AdminDashboardService::class, function ($app) {
            return new AdminDashboardService(
                $app->make(ApprovalService::class),
                $app->make(StockOpnameService::class),
                $app->make(TaskService::class),
                $app->make(InventoryService::class),
                $app->make(ProductService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'dashboard');
    }
}