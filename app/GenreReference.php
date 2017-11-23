<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenreReference extends Model
{
    protected $fillable = ['manga_id', 'genre_id'];

    public function genre()
    {
        return $this->hasOne('App\Genre', 'id', 'genre_id');
    }
}
