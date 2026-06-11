<?php

namespace App\Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Product\Domain\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\Repositories\ProductRepository;

class ProductServiceProvider extends ServiceProvider
{
    protected string $name = 'Product';
    protected string $nameLower = 'product';

    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../../Infrastructure/Migrations');
        $this->loadViewsFrom(__DIR__.'/../../Presentation/views', 'product');
        $this->registerPermissions();
    }

    protected function registerPermissions(): void
    {
        $permissions = [
            'view-products' => 'View Products',
            'create-products' => 'Create Products',
            'edit-products' => 'Edit Products',
            'delete-products' => 'Delete Products',
        ];

        foreach ($permissions as $name => $label) {
            // Permissions are registered via database seeder
        }
    }
}