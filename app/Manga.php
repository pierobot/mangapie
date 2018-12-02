<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use \Carbon\Carbon;

use App\ArtistReference;
use App\AuthorReference;
use App\GenreReference;
use App\Interfaces\ImageArchiveInterface;
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

    public function associatedNameReferences()
    {
        return $this->hasMany('App\AssociatedNameReference', 'manga_id', 'id');
    }

    public function getAssociatedNames()
    {
        $assocNames = [];

        $references = $this->associatedNameReferences->load('associatedName');
        foreach ($references as $reference) {
            array_push($assocNames, $reference->associatedName);
        }

        return $assocNames;
    }

    public function authorReferences()
    {
        return $this->hasMany('App\AuthorReference', 'manga_id', 'id');
    }

    public function getAuthors()
    {
        $authors = [];

        $references = $this->authorReferences->load('author');
        foreach ($references as $reference) {
            array_push($authors, $reference->author);
        }

        return $authors;
    }

    public function artistReferences()
    {
        return $this->hasMany('App\ArtistReference', 'manga_id', 'id');
    }

    public function getArtists()
    {
        $artists = [];

        $references = $this->artistReferences->load('artist');
        foreach ($references as $reference) {
            array_push($artists, $reference->artist);
        }

        return $artists;
    }

    public function genreReferences()
    {
        return $this->hasMany('App\GenreReference', 'manga_id', 'id');
    }

    public function getGenres()
    {
        $genres = [];

        $references = $this->genreReferences->load('genre');
        foreach ($references as $reference) {
            array_push($genres, $reference->genre);
        }

        return $genres;
    }

    public function scopeSearch($query, $keywords)
    {
        $results = $query->whereRaw("match(name) against(? in boolean mode)", [$keywords])->get();

        // get the associated names that match
        $assocNames = AssociatedName::search($keywords)->get();
        $assocArray = [];
        // convert the associated names to a manga object and store them in an array
        foreach ($assocNames as $assocName) {
            array_push($assocArray, $assocName->reference->manga);
        }

        // convert the associated name array to an Illuminate\Support\Collection and merge
        $results = $results->merge(collect($assocArray));

        return empty($keywords) ? $query : $results;
    }

    /**
     * Performs an advanced search based on the given data.
     * At least one of the parameters is required to not be null.
     *
     * @param array $genres An array of genre names.
     * @param string $author The name of an author.
     * @param string $artist The name of an artist.
     * @param string $keywords Keywords to match against.
     * @return \Illuminate\Support\Collection
     */
    public static function advancedSearch($genres, $author, $artist, $keywords)
    {
        $libraryIds = LibraryPrivilege::getIds();
        $collection = null;

        // get a Collection object depending on whether keywords are present
        if (empty($keywords) == false) {
            $collection = Manga::whereRaw("match(name) against(? in boolean mode)", [$keywords])->get();

            // get the associated names that match
            $assocNames = AssociatedName::search($keywords)->get();
            $assocArray = [];
            // convert the associated names to a manga object and store them in an array
            foreach ($assocNames as $assocName) {
                array_push($assocArray, $assocName->reference->manga);
            }

            // convert the associated name array to an Illuminate\Support\Collection and merge
            $collection = $collection->merge(collect($assocArray));
        } else {
            $collection = Manga::all();
        }

        // filter by library permissions
        $collection = $collection->whereIn('library_id', $libraryIds);

        // filter by genres
        $collection = $collection->filter(function ($manga) use ($genres) {
            if (empty($genres))
                return true;

            $keep = false;

            // if any of the genres match the ones in the request then we keep the manga
            foreach ($manga->genreReferences as $genreReference) {
                if (in_array($genreReference->genre->id, $genres) == true) {
                    $keep = true;
                    break;
                }
            }

            return $keep;
        });

        // filter by author and artist
        $collection = $collection->filter(function ($manga) use ($author, $artist) {
            if (empty($author) && empty($artist))
                return true;

            $keep = false;

            if (empty($author) == false) {
                // if any of the authors match the ones in the request then we keep the manga
                foreach ($manga->authorReferences as $authorReference) {
                    if (IntlString::strcmp($authorReference->author->name, $author) == 0) {
                        $keep = true;
                        break;
                    }
                }
            }

            if (empty($artist) == false) {
                // if any of the artists match the ones in the request then we keep the manga
                foreach ($manga->artistReferences as $artistReference) {
                    if (IntlString::strcmp($artistReference->artist->name, $artist) == 0) {
                        $keep = true;
                        break;
                    }
                }
            }

            return $keep;
        });

        return $collection;
    }

    public function scopeFromLibrary($library_ids)
    {
        return Manga::whereIn('library_id', $library_ids);
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
        $archives = $this->archives->sort(function ($left, $right) use ($sort) {
            return $sort == 'ascending' ? strnatcasecmp($left->getName(), $right->getName()) :
                                          strnatcasecmp($right->getName(), $left->getName());
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
