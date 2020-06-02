<?php

namespace App\Observers;

use App\Archive;
use App\Notifications\NewArchiveNotification;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class ArchiveObserver
{
    /**
     * @param Archive $archive
     */
    public function created(Archive $archive)
    {
        /*
         *  TODO: Should I optimize but further limit to MySQL using JSON_ARRAY_APPEND?
         *  https://dev.mysql.com/doc/refman/8.0/en/json-modification-functions.html#function_json-array-append
         */
        $archive = $archive->loadMissing(['manga:id,name']);

        // Craft a query that will get all the notifications that pertain to the series
//        $updateableQuery = DatabaseNotification::query()
//            ->select(['id', 'data', 'notifiable_type', 'notifiable_id'])
//            ->whereNull('read_at')
//            ->where('type', '=', NewArchiveNotification::class)
//            ->whereJsonContains('data->series->id', $archive->manga->id);
//
//        // Get a Collection of users that do not have a notification for the series
//        $updateableUsers = $updateableQuery->pluck('notifiable_id')->toArray();
        /** @var Collection $users */
        $users = $archive->manga->watchers()
            ->select(['users.id'])
//            ->whereNotIn('users.id', $updateableUsers)
            ->get();

        // Send a notification to the users that do not have a notification for the series
        \Notification::send($users, new NewArchiveNotification($archive));
    }
}
