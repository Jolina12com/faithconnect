<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;

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


        // Register custom middleware
        $this->app['router']->aliasMiddleware('cors', \App\Http\Middleware\HandleCors::class);
        
        // Register observers
        \Illuminate\Notifications\DatabaseNotification::observe(\App\Observers\NotificationObserver::class);
        
        Paginator::useBootstrap();
    }
}
