<?php

namespace App\Providers;

use App\Facades\Rest\RestClass;
use Illuminate\Support\ServiceProvider;

class RestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('restClass', fn () => new RestClass());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
