<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
     * @return Builder
     */
    public function manga()
    {
        $name = $this->name;

        return Manga::whereHas('authors', function (Builder $query) use ($name) {
                $query->select(['author_id', 'name'])->where('name', $name);
            })
            ->orWhereHas('artists', function (Builder $query) use ($name) {
                $query->select(['artist_id', 'name'])->where('name', $name);
            })
            ->distinct();
    }

    /**
     * Gets a hasManyThrough relationship of the manga the person has authored.
     *
     * @return HasManyThrough
     */
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

    /**
     * Gets a hasManyThrough relationship of the manga the person has illustrated.
     *
     * @return HasManyThrough
     */
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
