<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use \Symfony\Component\Finder\Finder;
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
    //
    protected $fillable = [
        'name',
        'path',
        'library_id',
        'mu_id',
        'type',
        'description',
        'year',
        'ignore_on_scan',
        'mu_name',
        'distance'
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

    public function forceDelete()
    {
        // delete all information and references that belongs to this manga

        $id = $this->getId();

        ArtistReference::where('manga_id', $id)->forceDelete();

        AuthorReference::where('manga_id', $id)->forceDelete();

        GenreReference::where('manga_id', $id)->forceDelete();

        Favorite::where('manga_id', $id)->forceDelete();

        ReaderHistory::where('manga_id', $id)->forceDelete();

        WatchNotification::where('manga_id', $id)->forceDelete();

        parent::forceDelete();
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

        $references = $this->associatedNameReferences;
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

        $references = $this->authorReferences;
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

        $references = $this->artistReferences;
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

        $references = $this->genreReferences;
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
     * @param $genres An array of genre names.
     * @param $author The name of an author.
     * @param $artist The name of an artist.
     * @param $keywords Keywords to match against.
     * @return \Illuminate\Support\Collection
     */
    public static function advancedSearch($genres, $author, $artist, $keywords)
    {
        $libraryIds = LibraryPrivilege::getIds();
        $collection = null;

        // get a Collection object depending on whether keywords are present
        if (empty($keywords) == false) {
            $collection = Manga::whereRaw("match(name) against(? in boolean mode)", [$keywords])->get();
        } else {
            $collection = Manga::all();
        }

        // get the associated names that match
        $assocNames = AssociatedName::search($keywords)->get();
        $assocArray = [];
        // convert the associated names to a manga object and store them in an array
        foreach ($assocNames as $assocName) {
            array_push($assocArray, $assocName->reference->manga);
        }

        // convert the associated name array to an Illuminate\Support\Collection and merge
        $collection = $collection->merge(collect($assocArray));

        // filter by library permissions
        $collection = $collection->whereIn('library_id', $libraryIds);

        // filter by genres
        $collection = $collection->filter(function ($manga) use ($genres) {
            if (empty($genres))
                return true;

            $keep = false;

            // if any of the genres match the ones in the request then we keep the manga
            foreach ($manga->getGenres() as $genre) {
                if (in_array($genre->getName(), $genres) == true) {
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
                foreach ($manga->getAuthors() as $author_) {
                    if (IntlString::strcmp($author_->getName(), $author) == 0) {
                        $keep = true;
                        break;
                    }
                }
            }

            if (empty($artist) == false) {
                // if any of the artists match the ones in the request then we keep the manga
                foreach ($manga->getArtists() as $artist_) {
                    if (IntlString::strcmp($artist_->getName(), $artist) == 0) {
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

    private function getNumberTokens($name)
    {
        if (mb_ereg_search_init($name, "\\d+") === false)
            return false;

        $tokens = [];

        if (mb_ereg_search() === false)
            return false;

        // get first token
        $result = mb_ereg_search_getregs();
        while ($result !== false) {
            array_push($tokens, intval($result[0]));
            // get next token
            $result = mb_ereg_search_regs();
        }

        return empty($tokens) != true ? $tokens : false;
    }

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

    public function getImage($archive_name, $page)
    {
        if ($page < 1)
            return false;

        // Get the first archive if no name is specified
        if (empty($archive_name) === true) {

            $archives = $this->getArchives();
            if (empty($archives) === true)
                return false;

            $archive_name = $archives[0]['name'];
        }

        $archive_path = $this->getPath() . '/' . $archive_name;
        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return false;

        $images = $archive->getImages();
        if (empty($images) === true)
            return false;

        usort($images, function ($left, $right) {
           return strnatcasecmp($left['name'], $right['name']);
        });

        $index = $images[$page - 1]['index'];
        $image = $archive->getInfo($index);
        $name = $image['name'];
        $size = 0;

        $mime = $this->getMIME($name);
        if ($mime === false)
            return false;

        $contents = $archive->getImage($index, $size);
        if ($contents === false)
            return false;

        return [
            'contents' => $contents,
            'size' => $size,
            'mime' => $mime
        ];
    }

    public function getImageAsUrl($archive_name, $page)
    {
        if ($page < 1)
            return false;

        // Get the first archive if no name is specified
        if (empty($archive_name) === true) {

            $archives = $this->getArchives();
            if (empty($archives) === true)
                return false;

            $archive_name = $archives[0]['name'];
        }

        $archive_path = $this->getPath() . '/' . $archive_name;
        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return false;

        $images = $archive->getImages();
        if (empty($images) === true)
            return false;

        usort($images, function ($left, $right) {
            return strnatcasecmp($left['name'], $right['name']);
        });

        $index = $images[$page - 1]['index'];
        $image = $archive->getInfo($index);
        $name = $image['name'];
        $size = 0;

        $mime = $this->getMIME($name);
        if ($mime === false)
            return false;

        $urlPath = $archive->getImageUrlPath($index, $size);
        if ($urlPath === false)
            return false;

        return [
            'urlPath' => $urlPath,
            'size' => $size,
            'mime' => $mime
        ];
    }

    private function convertSizeToReadable($bytes)
    {
        $sizes = [ 'B', 'KB', 'MB', 'GB' ];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return number_format(round($bytes, 2), 2) . ' ' . $sizes[$i];
    }

    public function getArchives($sort = 'ascending')
    {
        // get all the files in the path and filter by archives
        $files = Finder::create()->in($this->path)
                                 ->name('*.zip')
                                 ->name('*.cbz')
                                 ->name('*.rar')
                                 ->name('*.cbr');

        // sort by number tokens
        $files->sort(function ($left, $right) use ($sort) {
            return $sort == 'ascending' ? strnatcasecmp($left->getFilename(), $right->getFilename()) :
                                          strnatcasecmp($right->getFilename(), $left->getFilename());
        });

        $archives = [];
        foreach ($files as $file) {
            $archive = [];
            $archive['name'] = $file->getRelativePathname();
            $archive['size'] = $this->convertSizeToReadable($file->getSize());
            $time = Carbon::createFromTimestamp($file->getMTime());
            $archive['modified'] = $time->toDateTimeString();

            array_push($archives, $archive);
        }

        return $archives;
    }

    /**
     *  Gets the adjacent archive in relation to $name.
     *
     *  @param string $name The name of the current archive.
     *  @param bool $next Boolean value that indicates to get the next or previous archive.
     *  @return mixed On success, an object containing the name, size, and modified
     *                date of the archive; on failure, FALSE.
     */
    public function getAdjacentArchive($name, $next = true)
    {
        if (empty($name))
            return false;

        $archives = $this->getArchives();
        if (empty($archives))
            return false;

        // find the index of $name in $archives
        for ($i = 0, $max = count($archives) - 1; $i < $max; $i++) {
            // ensure we have a valid object
            if (array_key_exists('name', $archives[$i])) {
                // if the names match then we can get the next archive
                if ($archives[$i]['name'] == $name) {
                    if ($next === false) {
                        // previous archive wanted
                        // check if we were given the first archive
                        if ($i == 0)
                            break;

                        return $archives[$i - 1];
                    } else {
                        // next archive wanted
                        return $archives[$i + 1];
                    }
                }
            }
        }

        return false;
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

        $author = Author::where('name', $authorName)->firstOrFail();

        AuthorReference::where('manga_id', $this->getId())
                       ->where('author_id', $author->getId())
                       ->forceDelete();
    }

    public function deleteArtistReference($artistName)
    {
        if (empty($artistName) == true)
            return;

        $artist = Artist::where('name', $artistName)->firstOrFail();

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

        $author = Author::firstOrCreate([
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

        $artist = Artist::firstOrCreate([
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
