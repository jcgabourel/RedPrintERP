<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;
use Src\InventoryManagement\Infrastructure\Persistence\Eloquent\EloquentProductRepository;
use Src\InventoryManagement\Domain\Repositories\CategoryRepositoryInterface;
use Src\InventoryManagement\Infrastructure\Persistence\Eloquent\EloquentCategoryRepository;
use Src\InventoryManagement\Domain\Repositories\BrandRepositoryInterface;
use Src\InventoryManagement\Infrastructure\Persistence\Eloquent\EloquentBrandRepository;

class InventoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Product repository interface
        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        // Bind Category repository interface
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );

        // Bind Brand repository interface
        $this->app->bind(
            BrandRepositoryInterface::class,
            EloquentBrandRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}