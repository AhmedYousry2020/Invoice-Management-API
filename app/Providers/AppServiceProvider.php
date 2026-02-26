<?php

namespace App\Providers;

use App\Services\Taxes\MunicipalFeeCalculator;
use App\Services\Taxes\TaxService;
use App\Services\Taxes\VATCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->singleton(TaxService::class, function ($app) {
                return new TaxService([
                    $app->make(VATCalculator::class),
                    $app->make(MunicipalFeeCalculator::class),
                ]);
            });
            $this->app->bind(
                \App\Repositories\Contracts\ContractRepositoryInterface::class,
                \App\Repositories\Eloquent\ContractRepository::class
            );
            
            $this->app->bind(
                \App\Repositories\Contracts\InvoiceRepositoryInterface::class,
                \App\Repositories\Eloquent\InvoiceRepository::class
            );

            $this->app->bind(
                \App\Repositories\Contracts\PaymentRepositoryInterface::class,
                \App\Repositories\Eloquent\PaymentRepository::class
            );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
