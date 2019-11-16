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
        \URL::forceRootUrl(config('app.url'));

        \Blade::if('admin', function () {
            return auth()->check() && auth()->user()->hasRole('Administrator');
        });

        \Blade::if('editor', function () {
            return auth()->check() && auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Editor');
        });

        \Validator::extend('dateinterval', function ($attribute, $value, $parameters, $validator) {
            try {
                return CarbonInterval::fromString($value) != '';
            } catch (InvalidArgumentException $e) {
                return false;
            }
        });

        \Validator::extend('cron', function ($attribute, $value, $parameters, $validator) {
            return \Cron\CronExpression::isValidExpression($value);
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
