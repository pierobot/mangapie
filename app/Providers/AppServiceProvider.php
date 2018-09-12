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
            return \Auth::check() && \Auth::user()->isAdmin();
        });

        \Blade::if('maintainer', function () {
            return \Auth::check() && \Auth::user()->isAdmin() || \Auth::user()->isMaintainer();
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
