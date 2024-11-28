<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CupsService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(CupsService::class, function ($app) {
            return new CupsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }


    
}
