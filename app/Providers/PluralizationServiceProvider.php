<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\Common\Inflector\Inflector;

class PluralizationServiceProvider extends ServiceProvider {
    
    public function register()
    {
        Inflector::rules('plural', ['uninflected' => ['manga']]);
        Inflector::rules('plural', ['uninflected' => ['^(.*)information']]);

        Inflector::rules('plural', [
            'uninflected' => [
                'completed',
                'dropped',
                'on_?hold',
                'planned',
                'reading'
            ]
        ]);
    }
}