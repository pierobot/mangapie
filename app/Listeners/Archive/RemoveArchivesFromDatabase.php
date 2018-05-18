<?php

namespace App\Listeners\Archive;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Archive;
use App\Events\Archive\RemovedArchives;

class RemoveArchivesFromDatabase implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RemovedArchive  $event
     * @return void
     */
    public function handle(RemovedArchives $event)
    {
        foreach ($event->removedArchives as $archive) {
            Archive::where('manga_id', $archive['manga_id'])
                   ->where('name', $archive['name'])
                   ->forceDelete();
        }
    }
}
