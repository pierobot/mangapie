<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Symfony\Component\Finder\Finder;
use \Carbon\Carbon;

use \App\Manga;

class Library extends Model
{
    //
    protected $fillable = ['name', 'path'];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function manga()
    {
        return $this->hasMany('App\Manga', 'library_id', 'id');
    }
}
