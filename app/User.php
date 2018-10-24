<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'created_at', 'admin', 'maintainer'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @param array|mixed $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function admins($columns = ['*'])
    {
        return (new static)->newQuery()->where('admin', true)->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * @param array|mixed $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function maintainers($columns = ['*'])
    {
        return (new static)->newQuery()->where('maintainer', true)->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    public function isMaintainer()
    {
        return $this->maintainer;
    }

    public function getLastSeen()
    {
        return $this->last_seen;
    }

    public function getJoined()
    {
        return $this->created_at;
    }

    public function getAbout()
    {
        return $this->about;
    }

    public function favorites()
    {
        return $this->hasMany(\App\Favorite::class, 'user_id', 'id');
    }

    public function privileges()
    {
        return $this->hasMany(\App\LibraryPrivilege::class, 'user_id', 'id');
    }

    public function watchReferences()
    {
        return $this->hasMany(\App\WatchReference::class, 'user_id', 'id');
    }

    public function watchNotifications()
    {
        return $this->hasMany(\App\WatchNotification::class, 'user_id', 'id');
    }

    public function readerHistory()
    {
        return $this->hasMany(\App\ReaderHistory::class, 'user_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(\App\Vote::class, 'user_id', 'id');
    }

    public function archiveViews()
    {
        return $this->hasMany(\App\ArchiveView::class);
    }

    public function mangaViews()
    {
        return $this->hasMany(\App\MangaView::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class);
    }
}
