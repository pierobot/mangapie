<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Archive\NewArchives' => [
            'App\Listeners\Archive\AddArchivesToDatabase',
        ],

        'App\Events\Archive\RemovedArchives' => [
            'App\Listeners\Archive\RemoveArchivesFromDatabase'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \App\Archive::observe(\App\Observers\Archive::class);
    }
}
