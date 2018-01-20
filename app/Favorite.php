<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    //
    protected $fillable = ['user_id', 'manga_id'];

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getMangaId()
    {
        return $this->manga_id;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function manga()
    {
        return $this->belongsTo('App\Manga', 'manga_id', 'id');
    }
}
