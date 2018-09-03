<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArtistReference extends Model
{
    //
    protected $fillable = ['manga_id', 'artist_id'];

    public function getMangaId()
    {
        return $this->manga_id;
    }

    public function getArtistId()
    {
        return $this->artist_id;
    }

    public function artist()
    {
        return $this->belongsTo(Person::class);
    }

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
