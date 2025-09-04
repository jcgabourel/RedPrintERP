<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;
use Src\CustomerManagement\Infrastructure\Persistence\Eloquent\EloquentCustomerRepository;
use Src\CustomerManagement\Application\Services\CustomerService;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the repository interface to the Eloquent implementation
        $this->app->bind(
            CustomerRepositoryInterface::class,
            EloquentCustomerRepository::class
        );

        // Bind the CustomerService
        $this->app->bind(CustomerService::class, function ($app) {
            return new CustomerService(
                $app->make(CustomerRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
