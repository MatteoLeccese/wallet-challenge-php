<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentsService;
use App\Services\MailerService;
use App\Services\WalletService;


class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    { 
        $this->app->singletonIf(PaymentsService::class, function ($app) {
            // Inject MailerService, and WalletService into PaymentsService
            return new PaymentsService($app->make(MailerService::class),$app->make(WalletService::class));
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
