<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
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
            return \Auth::check() && \Auth::user()->hasRole('Administrator');
        });

        \Blade::if('editor', function () {
            return \Auth::check() && \Auth::user()->hasRole('Administrator') || \Auth::user()->hasRole('Editor');
        });

        \Validator::extend('can', function ($attribute, $value, $parameters, $validator) {
            $action = $parameters[0];
            $class = $parameters[1];
            $object = $class::find($value);

            return \Auth::user()->can($action, $object);
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
