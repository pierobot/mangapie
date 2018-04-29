<?php

namespace App\Providers;

use App\LogParser;
use Illuminate\Support\ServiceProvider;

class LogParserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('LogParser', function () {
            return new LogParser;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['LogParser'];
    }
}
