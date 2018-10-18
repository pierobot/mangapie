<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        \URL::forceRootUrl(config('app.url'));
        \URL::forceScheme('https');

        \Blade::if('admin', function () {
            return auth()->check() && auth()->user()->admin;
        });

        \Blade::if('maintainer', function () {
            return auth()->check() && auth()->user()->admin || auth()->user()->maintainer;
        });
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
