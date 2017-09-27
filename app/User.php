<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/*
        \App\User::firstOrCreate([
            'name' => 'dev',
            'email' => 'fake@email.com',
            'password' => '$2y$10$5q/qypVAXnS.qHF7A.C0ke9R5NM0.UHae3WbWIg60BSeBnynFi0m6',
            'admin' => true
        ]);
*/

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'admin', 'maintainer', 'theme'
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
}
