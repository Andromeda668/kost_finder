<?php

namespace App\Providers;

use App\Services\PaymentGateway\PaymentGatewayManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function () {
            return new PaymentGatewayManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Temporary debug override: when running locally, force session driver to 'file'
        // to avoid hitting the database for session storage during troubleshooting.
        if (config('app.env') === 'local') {
            config(['session.driver' => 'file']);
        }

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
