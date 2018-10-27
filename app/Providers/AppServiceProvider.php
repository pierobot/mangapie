<?php

namespace App\Providers;

use InvalidArgumentException;

use Carbon\CarbonInterval;
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
            return auth()->check() && auth()->user()->admin == true;
        });

        \Blade::if('maintainer', function () {
            return auth()->check() && auth()->user()->admin == true || auth()->user()->maintainer == true;
        });

        \Validator::extend('dateinterval', function ($attribute, $value, $parameters, $validator) {
            try {
                return CarbonInterval::fromString($value) != '';
            } catch (InvalidArgumentException $e) {
                return false;
            }
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
