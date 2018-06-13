<?php

namespace App\Listeners\Archive;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Archive;
use App\Events\Archive\NewArchives;

class AddArchivesToDatabase implements ShouldQueue
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
     * @param  NewArchive  $event
     * @return void
     */
    public function handle(NewArchives $event)
    {
        foreach ($event->newArchives as $archive) {
            Archive::updateOrCreate([
                'manga_id' => $archive['manga_id'],
                'name' => $archive['name'],
                'size' => $archive['size']
            ]);
        }
    }
}
