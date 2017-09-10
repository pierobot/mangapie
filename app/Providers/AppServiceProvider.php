<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \URL::forceRootUrl(config('app.url'));
        \URL::forceScheme('https');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
