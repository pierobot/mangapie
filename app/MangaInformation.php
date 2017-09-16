<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\Author;
use \App\AuthorReference;
use \App\Artist;
use \App\ArtistReference;
use \App\Genre;
use \App\GenreInformation;
use \App\Manga;
use \App\JaroWinkler;
use \App\MangaUpdates;

class MangaInformation extends Model
{
    //
    protected $fillable = ['id', 'mu_id', 'name', 'description', 'type', 'year'];

    public function getMangaId() {

        return $this->id;
    }

    public function getMangaUpdatesId() {

        return $this->mu_id;
    }

    public function getDescription() {

        return $this->description;
    }

    public function getType() {

        return $this->type;
    }

    private function updateMangaInformation($mu_info) {
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

    private function updateAssociatedNames($mu_info) {
        if ($mu_info == null)
            return false;

        $references = AssociatedNameReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('assoc_names', $mu_info) == false)
            return false;

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

    private function updateGenreInformation($mu_info) {
        if ($mu_info == null)
            return false;

        $genre_info = GenreInformation::where('manga_id', '=', $this->getMangaId());
        $genre_info->forceDelete();

        if (array_key_exists('genres', $mu_info) == false)
            return false;

        for ($i = 0; $i < sizeof($mu_info['genres']); $i++) {
            $genre_name = $mu_info['genres'][$i];
            $genre = Genre::where('name', '=', $genre_name)->first();

            GenreInformation::updateOrCreate([
                'manga_id' => $this->getMangaId(),
                'genre_id' => $genre->getId()
            ]);
        }

        return true;
    }

    private function updateArtistsInformation($mu_info) {
        if ($mu_info == null)
            return false;

        $references = ArtistReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('artists', $mu_info) == false)
            return false;

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

    private function updateAuthorsInformation($mu_info) {
        if ($mu_info == null)
            return false;

        $references = AuthorReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('authors', $mu_info) == false)
            return false;

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

    public static function createFromMangaUpdates($id, $name) {
        $manga_info = null;

        $search_results = [];
        $top_match = null;
        // search through five pages
        for ($i = 1; $i <= 5; $i++) {

            $results = MangaUpdates::search($name, $i);
            if ($results === false || $results == [])
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

                if ($manga_info->updateMangaInformation($mu_info) === false)
                    return null;

                if ($manga_info->updateAssociatedNames($mu_info) == false)
                    return null;

                if ($manga_info->updateGenreInformation($mu_info) === false)
                    return null;

                if ($manga_info->updateArtistsInformation($mu_info) == false)
                    return null;

                if ($manga_info->updateAuthorsInformation($mu_info) == false)
                    return null;

                $manga_info->save();
            }
        }

        return $manga_info;
    }

    public function updateFromMangaUpdates($mu_id) {
        // Update the values that don't depend on mangaupdates first
        // ...

        // Update those that depend on mangaupdates
        $mu_info = MangaUpdates::information($mu_id);
        if ($mu_info == null)
            return false;

        if ($this->updateMangaInformation($mu_info) === false)
            return false;

        if ($this->updateAssociatedNames($mu_info) == false)
            return false;

        if ($this->updateGenreInformation($mu_info) === false)
            return false;

        if ($this->updateArtistsInformation($mu_info) == false)
            return false;

        if ($this->updateAuthorsInformation($mu_info) == false)
            return false;

        return true;
    }

    public function getAssociatedNames() {
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

        return $assoc_names != [] ? $assoc_names : null;
    }

    public function getYear() {
        return $this->year;
    }

    public function getGenres() {
        $genres = [];
        $genre_info = GenreInformation::where('manga_id', '=', $this->getMangaId())->get();

        if ($genre_info == null)
            return null;

        for ($i = 0; $i < sizeof($genre_info); $i++) {
            $genre = Genre::find($genre_info[$i]['genre_id']);
            if ($genre == null)
                continue;

            $genres[$i] = $genre->getName();
        }

        return $genres != [] ? $genres : null;
    }

    public function getAuthors() {
        $references = AuthorReference::where('manga_id', '=', $this->getMangaId())->get();
        $authors = [];

        foreach ($references as $reference) {
            $author = Author::find($reference->getAuthorId());
            if ($author != null)
                array_push($authors, $author);
        }

        return $authors != [] ? $authors : null;
    }

    public function getArtists() {
        $references = ArtistReference::where('manga_id', '=', $this->getMangaId())->get();
        $artists = [];

        foreach ($references as $reference) {
            $artist = Artist::find($reference->getArtistId());
            if ($artist != null)
                array_push($artists, $artist);
        }

        return $artists != [] ? $artists : null;
    }
}
