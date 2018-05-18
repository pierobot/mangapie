<?php

namespace App\Observers;

use App\WatchNotification;
use App\WatchReference;

class Archive
{
    public function created(\App\Archive $archive)
    {
        $mangaId = $archive->manga->getId();
        // get a watch reference to all the users that are watching the manga
        $references = WatchReference::where('manga_id', $mangaId)->get();
        // create the notification
        foreach ($references as $reference) {
            WatchNotification::create([
                'user_id' => $reference->user->getId(),
                'manga_id' => $archive->manga->getId()
            ]);
        }
    }
}
