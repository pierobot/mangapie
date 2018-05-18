<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\NotificationInterface;

class WatchNotification
    extends Model
    implements NotificationInterface
{
    public $fillable = ['user_id', 'manga_id', 'archive_id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }

    public function archive()
    {
        return $this->belongsTo(\App\Archive::class);
    }

    /**
     * Gets the id of the notification.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the type of notification.
     *
     * @return string
     */
    public function getType()
    {
        return "watch";
    }

    /**
     * Gets the message for a WatchNotification.
     *
     * @return string
     */
    public function getMessage()
    {
        return "New archive(s)";
    }

    /**
     * Gets the manga this notification references.
     * @return \App\Manga
     */
    public function getData()
    {
        return $this->manga;
    }

    /**
     * Gets the datetime the notification was created.
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->created_at;
    }
}
