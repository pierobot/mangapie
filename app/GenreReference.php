<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenreReference extends Model
{
    protected $fillable = ['manga_id', 'genre_id'];

    public function genre()
    {
        return $this->belongsTo('App\Genre');
    }

    public function manga()
    {
        return $this->belongsTo('App\Manga');
    }
}
