<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register custom middleware
        $this->app['router']->aliasMiddleware('cors', \App\Http\Middleware\HandleCors::class);

        // Register observers
        \Illuminate\Notifications\DatabaseNotification::observe(\App\Observers\NotificationObserver::class);

        Paginator::useBootstrap();
    }
}