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
        'name', 'email', 'password', 'admin', 'maintainer', 'theme', 'ltr'
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

    public function favorites()
    {
        return $this->hasMany('App\Favorite', 'user_id', 'id');
    }

    public function privileges()
    {
        return $this->hasMany('App\LibraryPrivilege', 'user_id', 'id');
    }

    public function watchReferences()
    {
        return $this->hasMany(\App\WatchReference::class, 'user_id', 'id');
    }

    public function watchNotifications()
    {
        return $this->hasMany(\App\WatchNotification::class, 'user_id', 'id');
    }
}
