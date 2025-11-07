<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ProxyService;

class ProxyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProxyService::class, function ($app) {
            return new ProxyService();
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
