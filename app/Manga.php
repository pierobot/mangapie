<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Interfaces\EditableInterface;

class Manga
    extends Model
    implements EditableInterface
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

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

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public function getMangaUpdatesId()
    {
        return $this->mu_id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getIgnoreOnScan()
    {
        return $this->ignore_on_scan;
    }

    public function setIgnoreOnScan($value)
    {
        $this->ignore_on_scan = $value;
    }

    public function getMangaUpdatesName()
    {
        return $this->mu_name;
    }

    public function setMangaUpdatesName($name)
    {
        $this->mu_name = $name;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function setDistance($value)
    {
        $this->distance = $value;
    }

    public function getLastUpdated()
    {
        return $this->updated_at;
    }

    public function getCoverArchiveId()
    {
        return $this->cover_archive_id;
    }

    public function getCoverArchive()
    {
        return $this->archives->where('id', $this->getCoverArchiveId())->first();
    }

    public function getCoverPage()
    {
        return $this->cover_archive_page;
    }

    public function forceDelete()
    {
        // TODO: handle more elegantly using observers?

        // delete all information and references that belongs to this manga
        $this->artistReferences()->forceDelete();
        $this->authorReferences()->forceDelete();
        $this->genreReferences()->forceDelete();
        $this->favorites()->forceDelete();
        $this->archives()->forceDelete();
        $this->readerHistory()->forceDelete();
        $this->watchNotifications()->forceDelete();
        $this->votes()->forceDelete();

        parent::forceDelete();
    }

    public function archives()
    {
        return $this->hasMany(\App\Archive::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(\App\Favorite::class);
    }

    public function readerHistory()
    {
        return $this->hasMany(\App\ReaderHistory::class);
    }

    public function votes()
    {
        return $this->hasMany(\App\Vote::class);
    }

    public function watchNotifications()
    {
        return $this->hasMany(\App\WatchNotification::class);
    }

    public function library()
    {
        return $this->belongsTo('App\Library');
    }

    /**
     * Gets a HasManyThrough instance of all the associated names.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function associatedNames()
    {
        return $this->hasManyThrough(
            AssociatedName::class,
            AssociatedNameReference::class,
            'manga_id',
            'id',
            'id',
            'associated_name_id'
        );
    }

    /**
     * Gets a HasMany instance of all the associated name references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function associatedNameReferences()
    {
        return $this->hasMany(AssociatedNameReference::class);
    }

    /**
     * Gets a HasManyThrough instance of all the authors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function authors()
    {
        return $this->hasManyThrough(
            Person::class,
            AuthorReference::class,
            'manga_id',
            'id',
            'id',
            'author_id'
        );
    }

    /**
     * Gets a HasMany instance of all the author references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authorReferences()
    {
        return $this->hasMany(AuthorReference::class, 'manga_id', 'id');
    }

    /**
     * Gets a HasManyThrough instance of all the artists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function artists()
    {
        return $this->hasManyThrough(
            Person::class,
            ArtistReference::class,
            'manga_id',
            'id',
            'id',
            'artist_id'
        );
    }

    /**
     * Gets a HasMany instance of all the artist references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function artistReferences()
    {
        return $this->hasMany('App\ArtistReference', 'manga_id', 'id');
    }

    /**
     * Gets a HasManyThrough instance of all the genres.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function genres()
    {
        return $this->hasManyThrough(
            Genre::class,
            GenreReference::class,
            'manga_id',
            'id',
            'id',
            'genre_id'
        );
    }

    /**
     * Gets a HasMany instance of all the genre references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function genreReferences()
    {
        return $this->hasMany('App\GenreReference', 'manga_id', 'id');
    }

    /**
     * Gets an instance of Builder that performs a full text search based on the keywords.
     *
     * @param string $keywords
     * @return Builder
     */
    public static function search(string $keywords)
    {
        return Manga::whereHas('associatedNames', function (Builder $query) use ($keywords) {
            $query->whereRaw('match(name) against(? in boolean mode)', [$keywords]);
        });
    }

    /**
     * Performs an advanced search based on the given data.
     *
     * @param int[] $genres An array of genre names.
     * @param string $author The name of an author.
     * @param string $artist The name of an artist.
     * @param string $keywords Keywords to match against.
     * @return Builder
     */
    public static function advancedSearch(array $genres, string $author, string $artist, string $keywords)
    {
        // match by keywords if present
        if (! empty($keywords)) {
            /** @var Builder $items */
            $items = Manga::search($keywords);

        } else {
            // no keywords were given so get a clean query to build from
            /** @var Builder $items */
            $items = Manga::query();
        }

        // filter by genre ids
        if (! empty($genres)) {
            $items = $items->whereHas('genres', function (Builder $query) use ($genres) {
                $query->whereIn('id', $genres);
            });
        }

        // filter by author name
        if (! empty($author)) {
            $items = $items->whereHas('authors', function (Builder $query) use ($author) {
                $query->where('name', $author)
                    // TODO: refactor for fulltext instead of using the like operator?
                    ->orWhere('name', 'like', "%$author%");
            });
        }

        // filter by artist name
        if (! empty($artist)) {
            // TODO: add in fulltext search ?
            $items = $items->whereHas('artists', function (Builder $query) use ($artist) {
                $query->where('name', $artist)
                    // TODO: refactor for fulltext instead of using the like operator?
                    ->orWhere('name', 'like', "%$artist%");
            });
        }

        return $items;
    }

//    private function getNumberTokens($name)
//    {
//        if (mb_ereg_search_init($name, "\\d+") === false)
//            return false;
//
//        $tokens = [];
//
//        if (mb_ereg_search() === false)
//            return false;
//
//        // get first token
//        $result = mb_ereg_search_getregs();
//        while ($result !== false) {
//            array_push($tokens, intval($result[0]));
//            // get next token
//            $result = mb_ereg_search_regs();
//        }
//
//        return empty($tokens) != true ? $tokens : false;
//    }

    private function getMIME($image_name)
    {
        $image_extensions = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif' // really rare but I've come across this.
        ];

        $extension = ImageArchive::getExtension($image_name);
        if ($extension === false)
            return false;

        return array_key_exists($extension, $image_extensions) == true ? $image_extensions[$extension] : false;
    }

    public function getImage(Archive $archive, $page)
    {
        if ($page < 1 || empty($archive))
            return false;

        $archive_name = $archive->getName();
        $archive_path = $this->getPath() . '/' . $archive_name;

        $imgArchive = ImageArchive::open($archive_path);
        if ($imgArchive === false || $imgArchive->good() === false)
            return false;

        $images = $imgArchive->getImages();
        if (empty($images) === true)
            return false;

        usort($images, function ($left, $right) {
           return strnatcasecmp($left['name'], $right['name']);
        });

        if ($page > count($images))
            return false;

        $index = $images[$page - 1]['index'];
        $image = $imgArchive->getInfo($index);
        $name = $image['name'];
        $size = 0;

        $mime = $this->getMIME($name);
        if ($mime === false)
            return false;

        $contents = $imgArchive->getImage($index, $size);
        if ($contents === false)
            return false;

        return [
            'contents' => $contents,
            'size' => $size,
            'mime' => $mime
        ];
    }

    public function getImageAsUrl(Archive $archive, $page)
    {
        if ($page < 1 || empty($archive))
            return false;

        $archive_name = $archive->getName();

        $archive_path = $this->getPath() . '/' . $archive_name;
        $imgArchive = ImageArchive::open($archive_path);
        if ($imgArchive === false)
            return false;

        $images = $imgArchive->getImages();
        if (empty($images) === true)
            return false;

        usort($images, function ($left, $right) {
            return strnatcasecmp($left['name'], $right['name']);
        });

        if ($page > count($images))
            return false;

        $index = $images[$page - 1]['index'];
        $image = $imgArchive->getInfo($index);
        $name = $image['name'];
        $size = 0;

        $mime = $this->getMIME($name);
        if ($mime === false)
            return false;

        $urlPath = $imgArchive->getImageUrlPath($index, $size);
        if ($urlPath === false)
            return false;

        return [
            'urlPath' => $urlPath,
            'size' => $size,
            'mime' => $mime
        ];
    }

    /**
     * @param string $sort
     * @return mixed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getArchives($sort = 'ascending')
    {
        // TODO: deprecate this function and have the callees sort the archives.
        $archives = $this->archives->sort(function (Archive $left, Archive $right) use ($sort) {
            return $sort == 'ascending' ? strnatcasecmp($left->name, $right->name) :
                                          strnatcasecmp($right->name, $left->name);
        });

        return $archives;
    }

    public function setMangaUpdatesId($id)
    {
        $this->mu_id = $id;
    }

    public function deleteType()
    {
        $this->type = null;
    }

    public function deleteDescription()
    {
        $this->description = null;
    }

    public function deleteAssociatedName($name)
    {
        if (empty($name) == true)
            return;

        $assocName = AssociatedName::where('name', $name)->firstOrFail();

        AssociatedNameReference::where('manga_id', $this->getId())
                               ->where('assoc_name_id', $assocName->getId())
                               ->forceDelete();

        $refCount = AssociatedNameReference::where('assoc_name_id', $assocName->getId())->count();
        if ($refCount == 0) {
            $assocName->forceDelete();
        }
    }

    public function deleteAuthorReference($authorName)
    {
        if (empty($authorName) == true)
            return;

        $author = Person::where('name', $authorName)->firstOrFail();

        AuthorReference::where('manga_id', $this->getId())
                       ->where('author_id', $author->getId())
                       ->forceDelete();
    }

    public function deleteArtistReference($artistName)
    {
        if (empty($artistName) == true)
            return;

        $artist = Person::where('name', $artistName)->firstOrFail();

        ArtistReference::where('manga_id', $this->getId())
                       ->where('artist_id', $artist->getId())
                       ->forceDelete();
    }

    public function deleteGenreReference($genreName)
    {
        if (empty($genreName) == true)
            return;

        $genre = Genre::where('name', $genreName)->firstOrFail();

        GenreReference::where('manga_id', $this->getId())
                      ->where('genre_id', $genre->getId())
                      ->forceDelete();
    }

    public function deleteYear()
    {
        $this->year = null;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function addAssociatedName($name)
    {
        if (empty($name) == true)
            return;

        $assocName = AssociatedName::firstOrCreate([
            'name' => $name
        ]);

        AssociatedNameReference::firstOrCreate([
            'manga_id' => $this->getId(),
            'assoc_name_id' => $assocName->getId()
        ]);
    }

    public function addAssociatedNames($names)
    {
        if (empty($names) == true)
            return;

        foreach ($names as $name) {
            $this->addAssociatedName($name);
        }
    }

    public function addAuthor($authorName)
    {
        if (empty($authorName) == true)
            return;

        $author = Person::firstOrCreate([
            'name' => $authorName
        ]);

        AuthorReference::firstOrCreate([
            'manga_id' => $this->getId(),
            'author_id' => $author->getId()
        ]);
    }

    public function addAuthors($authorNames)
    {
        if (empty($authorNames) == true)
            return;

        foreach ($authorNames as $name) {
            $this->addAuthor($name);
        }
    }

    public function addArtist($artistName)
    {
        if (empty($artistName) == true)
            return;

        $artist = Person::firstOrCreate([
            'name' => $artistName
        ]);

        ArtistReference::firstOrCreate([
            'manga_id' => $this->getId(),
            'artist_id' => $artist->getId()
        ]);
    }

    public function addArtists($artistNames)
    {
        if (empty($artistNames) == true)
            return;

        foreach ($artistNames as $name) {
            $this->addArtist($name);
        }
    }

    public function addGenre($genreName)
    {
        if (empty($genreName) == true)
            return;

        $genre = Genre::where('name', $genreName)->firstOrFail();

        GenreReference::firstOrCreate([
            'manga_id' => $this->getId(),
            'genre_id' => $genre->getId()
        ]);
    }

    public function addGenres($genreNames)
    {
        if (empty($genreNames) == true)
            return;

        foreach ($genreNames as $name) {
            $this->addGenre($name);
        }
    }

    public function setYear($year)
    {
        $this->year = $year;
    }
}
