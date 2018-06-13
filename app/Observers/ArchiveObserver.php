<?php

namespace App\Observers;

use App\Archive;
use App\WatchNotification;
use App\WatchReference;

class ArchiveObserver
{
    public function created(Archive $archive)
    {
        $mangaId = $archive->manga->getId();
        // get a watch reference to all the users that are watching the manga
        $references = WatchReference::where('manga_id', $mangaId)->get();
        // create the notification
        foreach ($references as $reference) {
            WatchNotification::create([
                'user_id' => $reference->user->getId(),
                'manga_id' => $archive->manga->getId(),
                'archive_id' => $archive->getId()
            ]);
        }
    }
}
