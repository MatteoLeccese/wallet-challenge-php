<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WalletService;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(WalletService::class, function ($app) {
            return new WalletService();
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
