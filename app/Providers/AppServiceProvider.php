<?php

namespace App\Providers;

use App\Contracts\ExternalTicketServiceInterface;
use App\Services\External\FakeExternalTicketServiceAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ExternalTicketServiceInterface::class,
            FakeExternalTicketServiceAdapter::class
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
