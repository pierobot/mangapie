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

    public function forceDelete()
    {
        // get all the manga that have library_id to ours
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        // and delete them
        foreach ($manga as $manga_) {
            // Manga::forceDelete deletes all the references to other tables (artists, authors, manga_information, etc..)
            $manga_->forceDelete();
        }

        parent::forceDelete();
    }
}
