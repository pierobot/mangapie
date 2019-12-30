<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
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
     * @return Collection
     */
    public function manga()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $authorReferences */
        $authorReferences = AuthorReference::where('author_id', $this->id);
        $artistReferences = ArtistReference::where('artist_id', $this->id);

        $references = $authorReferences->joinSub(
                $artistReferences,
                'artist_references',
                'author_references.author_id', '=', 'artist_references.artist_id')
            ->get()
            ->unique('manga_id')
            ->load('manga', 'manga.library');

        return $references->map(function (AuthorReference $reference) {
            return $reference->manga;
        });
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
