<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MailerService;

class MailerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MailerService::class, function ($app) {
            return new MailerService();
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
