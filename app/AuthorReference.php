<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthorReference extends Model
{
    //
    protected $fillable = ['manga_id', 'author_id'];

    public function getMangaId()
    {
        return $this->manga_id;
    }

    public function getAuthorId()
    {
        return $this->author_id;
    }

    public function author()
    {
        return $this->belongsTo('App\Author');
    }

    public function manga()
    {
        return $this->belongsTo('App\Manga');
    }
}
