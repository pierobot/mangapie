<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = ['name'];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets a Collection of the manga a person is involved with.
     * Includes both author and artist.
     *
     * @return Collection
     */
    public function manga()
    {
        // TODO: Optimize into a single query ?

        $whereAuthor = $this->mangaWhereAuthor;
        $whereArtist = $this->mangaWhereArtist;

        return $whereAuthor->merge($whereArtist)->unique('id');
    }

    public function mangaWhereAuthor()
    {
        return $this->hasManyThrough(
            Manga::class,
            AuthorReference::class,
            'author_id',
            'id',
            'id',
            'manga_id');
    }

    public function mangaWhereArtist()
    {
        return $this->hasManyThrough(
            Manga::class,
            ArtistReference::class,
            'artist_id',
            'id',
            'id',
            'manga_id');
    }
}
