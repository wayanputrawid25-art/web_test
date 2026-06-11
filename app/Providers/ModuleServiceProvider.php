<?php

namespace App\Providers;

use App\Modules\Approval\Providers\ApprovalServiceProvider;
use App\Modules\Dashboard\Admin\Providers\AdminDashboardServiceProvider;
use App\Modules\Dashboard\Operator\Providers\OperatorDashboardServiceProvider;
use App\Modules\Inventory\Providers\InventoryServiceProvider;
use App\Modules\Product\Providers\ProductServiceProvider;
use App\Modules\StockOpname\Providers\StockOpnameServiceProvider;
use App\Modules\TaskCenter\Providers\TaskCenterServiceProvider;
use App\Modules\Users\Providers\UsersServiceProvider;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    protected array $providers = [
        UsersServiceProvider::class,
        ProductServiceProvider::class,
        InventoryServiceProvider::class,
        TaskCenterServiceProvider::class,
        StockOpnameServiceProvider::class,
        ApprovalServiceProvider::class,
        OperatorDashboardServiceProvider::class,
        AdminDashboardServiceProvider::class,
    ];

    public function register(): void
    {
        $this->registerModules();
    }

    public function boot(): void
    {
        //
    }

    protected function registerModules(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}