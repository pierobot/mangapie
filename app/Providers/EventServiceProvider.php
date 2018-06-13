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
        \App\Events\Archive\NewArchives::class => [
            \App\Listeners\Archive\AddArchivesToDatabase::class
        ],

        \App\Events\Archive\RemovedArchives::class => [
            \App\Listeners\Archive\RemoveArchivesFromDatabase::class
        ],
    ];

    protected $subscribe = [
        \App\Listeners\ArchiveEventSubscriber::class,
        \App\Listeners\DirectoryEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \App\Archive::observe(\App\Observers\ArchiveObserver::class);
        \App\Manga::observe(\App\Observers\MangaObserver::class);
    }
}
