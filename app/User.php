<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'admin', 'maintainer', 'theme', 'ltr', 'about', 'last_seen'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->save();
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

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        $this->save();
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
        $this->save();
    }

    public function getLtr()
    {
        return $this->ltr;
    }

    public function setLtr($ltr)
    {
        $this->ltr = $ltr;
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
}
