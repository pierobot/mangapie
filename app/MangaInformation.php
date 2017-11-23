<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\Author;
use \App\AuthorReference;
use \App\Artist;
use \App\ArtistReference;
use \App\Genre;
use \App\GenreReference;
use \App\Manga;
use \App\JaroWinkler;
use \App\MangaUpdates;

class MangaInformation extends Model
{
    protected $fillable = ['id', 'mu_id', 'name', 'description', 'type', 'year'];

    public function getMangaId()
    {
        return $this->id;
    }

    public function getMangaUpdatesId()
    {
        return $this->mu_id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getLastUpdated()
    {
        return $this->updated_at;
    }

    private function updateMangaInformation($mu_info)
    {
        if ($mu_info == null)
            return false;

        $this->update([
            'mu_id' => $mu_info['mu_id'],
            'description' => $mu_info['description'],
            'type' => $mu_info['type'],
            'year' => $mu_info['year']
        ]);

        return true;
    }

    private function updateAssociatedNames($mu_info)
    {
        if ($mu_info == null)
            return false;

        $references = AssociatedNameReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('assoc_names', $mu_info) == false || $mu_info['assoc_names'] == null)
            return true;

        foreach ($mu_info['assoc_names'] as $name) {
            $assoc_name = AssociatedName::create([
                'name' => $name
            ]);

            $reference = AssociatedNameReference::create([
                'manga_id' => $this->getMangaId(),
                'assoc_name_id' => $assoc_name->getId()
            ]);
        }

        return true;
    }

    private function updateGenreReferences($mu_info)
    {
        if ($mu_info == null)
            return false;

        $genre_info = GenreReference::where('manga_id', '=', $this->getMangaId());
        $genre_info->forceDelete();

        if (array_key_exists('genres', $mu_info) == false || $mu_info['genres'] == null)
            return true;

        for ($i = 0; $i < sizeof($mu_info['genres']); $i++) {
            $genre_name = $mu_info['genres'][$i];
            $genre = Genre::where('name', '=', $genre_name)->first();

            GenreReference::updateOrCreate([
                'manga_id' => $this->getMangaId(),
                'genre_id' => $genre->getId()
            ]);
        }

        return true;
    }

    private function updateArtistsInformation($mu_info)
    {
        if ($mu_info == null)
            return false;

        $references = ArtistReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('artists', $mu_info) == false || $mu_info['artists'] == null)
            return true;

        for ($i = 0; $i < sizeof($mu_info['artists']); $i++) {
            $artist_name = $mu_info['artists'][$i];

            $artist = Artist::create([
                'name' => $artist_name
            ]);

            $reference = ArtistReference::create([
                'manga_id' => $this->getMangaId(),
                'artist_id' => $artist->getId()
            ]);
        }

        return true;
    }

    private function updateAuthorsInformation($mu_info)
    {
        if ($mu_info == null)
            return false;

        $references = AuthorReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('authors', $mu_info) == false || $mu_info['authors'] == null)
            return true;

        for ($i = 0; $i < sizeof($mu_info['authors']); $i++) {
            $author_name = $mu_info['authors'][$i];

            $author = Author::create([
                'name' => $author_name
            ]);

            $reference = AuthorReference::create([
                'manga_id' => $this->getMangaId(),
                'author_id' => $author->getId()
            ]);
        }

        return true;
    }

    public static function createFromMangaUpdates($id, $name)
    {
        $manga_info = null;

        $search_results = [];
        $top_match = null;
        // search through five pages
        for ($i = 1; $i <= 5; $i++) {

            $results = MangaUpdates::search($name, $i);
            if ($results === false || empty($results))
                break;

            // avoid getting other pages if we have a perfect match
            if ($results[0]['distance'] == 1.0) {

                $top_match = $results[0];
                break;
            }

            // a perfect match wasn't found, just append
            foreach ($results as $result) {

                array_push($search_results, $result);
            }
        }

        if ($top_match == null) {
            // sort descending
            usort($search_results, function ($left, $right) {
                if ($left['distance'] == $right['distance'])
                    return 0;
                elseif ($left['distance'] < $right['distance'])
                    return 1;
                elseif ($left['distance'] > $right['distance'])
                    return -1;
            });

            if (count($search_results) > 0) {
                $top_match = $search_results[0];
            }
        }

        if ($top_match != null) {

            $mu_id = $top_match['mu_id'];
            $mu_info = MangaUpdates::information($mu_id);

            if ($mu_info != null) {
                MangaInformation::create([
                    'id' => $id
                ]);

                $manga_info = MangaInformation::find($id);

                $manga_info->updateMangaInformation($mu_info);
                $manga_info->updateAssociatedNames($mu_info);
                $manga_info->updateGenreReferences($mu_info);
                $manga_info->updateArtistsInformation($mu_info);
                $manga_info->updateAuthorsInformation($mu_info);

                $manga_info->save();
            }
        }

        return $manga_info;
    }

    public function updateFromMangaUpdates($mu_id)
    {
        // Update the values that don't depend on mangaupdates first
        // ...

        // Update those that depend on mangaupdates
        $mu_info = MangaUpdates::information($mu_id);
        if ($mu_info == null)
            return false;

        $this->updateMangaInformation($mu_info);
        $this->updateAssociatedNames($mu_info);
        $this->updateGenreReferences($mu_info);
        $this->updateArtistsInformation($mu_info);
        $this->updateAuthorsInformation($mu_info);

        return true;
    }

    public function getAssociatedNames()
    {
        $assoc_names = [];
        $name_references = AssociatedNameReference::where('manga_id', '=', $this->getMangaId())->get();

        if ($name_references == null)
            return null;

        foreach ($name_references as $reference) {
            $assoc_name = AssociatedName::find($reference->getAssociatedNameId());
            if ($assoc_name == null)
                continue;

            array_push($assoc_names, $assoc_name);
        }

        return empty($assoc_names) !== true ? $assoc_names : null;
    }

    public function getGenres()
    {
        $genres = [];
        $genre_info = GenreReference::where('manga_id', '=', $this->getMangaId())->get();

        if ($genre_info == null)
            return null;

        for ($i = 0; $i < sizeof($genre_info); $i++) {
            $genre = Genre::find($genre_info[$i]['genre_id']);
            if ($genre == null)
                continue;

            $genres[$i] = $genre->getName();
        }

        return empty($genres) !== true ? $genres : null;
    }

    public function getAuthors()
    {
        $references = AuthorReference::where('manga_id', '=', $this->getMangaId())->get();
        $authors = [];

        if ($references == null)
            return null;

        foreach ($references as $reference) {
            $author = Author::find($reference->getAuthorId());
            if ($author != null)
                array_push($authors, $author);
        }

        return empty($authors) !== true ? $authors : null;
    }

    public function getArtists()
    {
        $references = ArtistReference::where('manga_id', '=', $this->getMangaId())->get();
        $artists = [];

        if ($references == null)
            return null;

        foreach ($references as $reference) {
            $artist = Artist::find($reference->getArtistId());
            if ($artist != null)
                array_push($artists, $artist);
        }

        return empty($artists) != true ? $artists : null;
    }

    /**
     * Updates the description for a manga.
     *
     * @param $description The description to update with.
     * @return bool
     */
    public function updateDescription($description)
    {
        $this->description = $description;

        return true;
    }

    /**
     * Deletes the description from a manga.
     *
     * @return bool
     */
    public function deleteDescription()
    {
        $this->description = null;

        return true;
    }

    /**
     * Updates the type of a manga.
     *
     * @param $type The type to update with.
     * @return bool
     */
    public function updateType($type)
    {
        $this->type = $type;

        return true;
    }

    public function deleteType()
    {
        $this->type = null;

        return true;
    }

    /**
     * Adds an associated name to a manga.
     *
     * @param $name The associated name to add.
     * @return bool
     */
    public function addAssociatedName($name)
    {
        $assoc_name = AssociatedName::firstOrCreate([
            'name' => $name
        ]);

        if ($assoc_name == null)
            return false;

        $reference = AssociatedNameReference::firstOrCreate([
            'manga_id' => $this->getMangaId(),
            'assoc_name_id' => $assoc_name->getId()
        ]);

        if ($reference == null) {
            $this->deleteAssociatedName($assoc_name->getName());
        }

        return true;
    }

    /**
     * Deletes an associated name from a manga.
     *
     * @param $name The associated name to delete.
     * @return bool
     */
    public function deleteAssociatedName($name)
    {
        // the name should always exist so throw an exception if it doesn't
        $assoc_name = AssociatedName::where('name', $name)->firstOrFail();

        AssociatedNameReference::where('manga_id', $this->getMangaId())
                               ->where('assoc_name_id', $assoc_name->getId())
                               ->forceDelete();

        // find other existing references and preserve
        $refCount = AssociatedNameReference::where('assoc_name_id', $assoc_name->getId())->count();
        if ($refCount == 0) {
            $assoc_name->forceDelete();
        }

        return true;
    }

    /**
     * Adds a genre to the manga.
     *
     * @param $name The name of the genre to add.
     * @return bool
     */
    public function addGenre($name)
    {
        // genre should always exist
        $genre = Genre::where('name', $name)->firstOrFail();

        $genreInfo = GenreReference::firstOrCreate([
            'manga_id' => $this->getMangaId(),
            'genre_id' => $genre->getId()
        ]);

        return $genreInfo != null;
    }

    /**
     * Deletes a genre from a manga.
     *
     * @param $name The name of the genre to delete.
     * @return bool
     */
    public function deleteGenre($name)
    {
        $genre = Genre::where('name', $name)->firstOrFail();

        GenreReference::where('manga_id', $this->getMangaId())
                        ->where('genre_id', $genre->getId())
                        ->forceDelete();

        return true;
    }

    /**
     * Adds an author to a manga.
     *
     * @param $name The name of the author.
     * @return bool
     */
    public function addAuthor($name)
    {
        $author = Author::firstOrCreate([
            'name' => $name
        ]);

        if ($author == null)
            return false;

        $reference = AuthorReference::firstOrCreate([
            'manga_id' => $this->getMangaId(),
            'author_id' => $author->getId()
        ]);

        if ($reference == null) {
            $this->deleteAuthor($author->getName());

            return false;
        }

        return true;
    }

    /**
     * Deletes an author from a manga.
     *
     * @param $name The name of the author.
     * @return bool
     */
    public function deleteAuthor($name)
    {
        $author = Author::where('name', $name)->firstOrFail();

        AuthorReference::where('manga_id', $this->getMangaId())
                       ->where('author_id', $author->getId())
                       ->forceDelete();

        // delete if there are no references
        $refCount = AuthorReference::where('author_id', $author->getId())->count();
        if ($refCount == 0) {
            $author->forceDelete();
        }

        return true;
    }

    /**
     * Adds an artist to a manga.
     *
     * @param $name The name of the artist.
     * @return bool
     */
    public function addArtist($name)
    {
        $artist = Artist::firstOrCreate([
            'name' => $name
        ]);

        if ($artist == null)
            return false;

        $reference = ArtistReference::firstOrCreate([
            'manga_id' => $this->getMangaId(),
            'artist_id' => $artist->getId()
        ]);

        if ($reference == null) {
            $this->deleteArtist($artist->getName());

            return false;
        }

        return true;
    }

    /**
     * Deletes an artist from a manga.
     *
     * @param $name The name of the artist.
     * @return bool
     */
    public function deleteArtist($name)
    {
        $artist = Artist::where('name', $name)->firstOrFail();

        ArtistReference::where('manga_id', $this->getMangaId())
                       ->where('artist_id', $artist->getId())
                       ->forceDelete();

        // delete if there are no references
        $refCount = ArtistReference::where('artist_id', $artist->getId())->count();
        if ($refCount == 0) {
            $artist->forceDelete();
        }

        return true;
    }

    /**
     * Updates the year for a manga.
     *
     * @param $year The year to update with.
     * @return bool
     */
    public function updateYear($year)
    {
        $this->year = $year;

        return true;
    }

    /**
     * Deletes the year from a manga.
     *
     * @return bool
     */
    public function deleteYear()
    {
        $this->year = null;

        return true;
    }
}
