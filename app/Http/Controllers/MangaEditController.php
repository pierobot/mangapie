<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Manga;
use App\Genre;
use App\GenreInformation;
use App\Http\Requests\EditMangaRequest;
use App\MangaUpdates;

class MangaEditController extends Controller
{
    public function index(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $mu_id = $manga->getMangaUpdatesId();

        return view('edit.manga.index')
            ->with('id', $id)
            ->with('mu_id', $mu_id)
            ->with('name', $name);
    }

    public function update(EditMangaRequest $request)
    {
        $id = \Input::get('id');
        $action = \Input::get('action');
        $manga = Manga::findOrFail($id);

        $response = \Response::make('', 500);

        if ($action == 'autofill') {
            $result = MangaUpdates::autofillFromId($manga, \Input::get('mu_id'));

            if ($result == false) {
                $response = \Redirect::action('MangaEditController@index')
                                     ->withErrors(['autofill' => 'Unable to scrape mangaupdates for information.']);
            } else {
                \Session::flash('success', 'The information was autofilled.');

                $response = \Redirect::action('MangaEditController@mangaupdates', [$id]);
            }
        } elseif ($action == 'description.update') {
            $manga->setDescription(\Input::get('description'));

            \Session::flash('success', 'The description was successfully updated.');

            $response = \Redirect::action('MangaEditController@description', [$id]);
        } elseif ($action == 'description.delete') {
            $manga->deleteDescription();

            \Session::flash('success', 'The description was successfully deleted.');

            $response = \Redirect::action('MangaEditController@description', [$id]);
        } elseif ($action == 'type.update') {
            $manga->setType(\Input::get('type'));

            \Session::flash('success', 'The type was successfully updated.');

            $response = \Redirect::action('MangaEditController@type', [$id]);
        } elseif ($action == 'type.delete') {
            $manga->deleteType();

            \Session::flash('success', 'The type was successfully deleted.');

            $response = \Redirect::action('MangaEditController@type', [$id]);
        } elseif ($action == 'assoc_name.add') {
            $manga->addAssociatedName(\Input::get('assoc_name'));

            \Session::flash('success', 'The associated name was successfully added.');

            $response = \Redirect::action('MangaEditController@type', [$id]);
        } elseif ($action == 'assoc_name.delete') {
            $manga->deleteAssociatedName(\Input::get('assoc_name'));

            \Session::flash('success', 'The associated name was successfully deleted.');

            $response = \Redirect::action('MangaEditController@names', [$id]);
        } elseif ($action == 'genre.add') {
            $manga->addGenre(\Input::get('genre'));

            \Session::flash('success', 'The genre was successfully added.');

            $response = \Redirect::action('MangaEditController@genres', [$id]);
        } elseif ($action == 'genre.delete') {
            $manga->deleteGenreReference(\Input::get('genre'));

            \Session::flash('success', 'The genre was successfully deleted.');

            $response = \Redirect::action('MangaEditController@genres', [$id]);
        } elseif ($action == 'author.add') {
            $manga->addAuthor(\Input::get('author'));

            \Session::flash('success', 'The author was successfully added.');

            $response = \Redirect::action('MangaEditController@authors', [$id]);
        } elseif ($action =='author.delete') {
            $manga->deleteAuthorReference(\Input::get('author'));

            \Session::flash('success', 'The author was successfully deleted.');

            $response = \Redirect::action('MangaEditController@authors', [$id]);
        } elseif ($action == 'artist.add') {
            $manga->addArtist(\Input::get('artist'));

            \Session::flash('success', 'The artist was successfully added.');

            $response = \Redirect::action('MangaEditController@artists', [$id]);
        } elseif ($action == 'artist.delete') {
            $manga->deleteArtistReference(\Input::get('artist'));

            \Session::flash('success', 'The artist was successfully deleted.');

            $response = \Redirect::action('MangaEditController@artists', [$id]);
        } elseif ($action == 'year.update') {
            $manga->setYear(\Input::get('year'));

            \Session::flash('success', 'The year was successfully updated.');

            $response = \Redirect::action('MangaEditController@year', [$id]);
        } elseif ($action == 'year.delete') {
            $manga->deleteYear();

            \Session::flash('success', 'The year was successfully deleted.');

            $response = \Redirect::action('MangaEditController@year', [$id]);
        }

        $manga->save();

        return $response;
    }

    public function mangaupdates(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();

        return view('edit.manga.mangaupdates')
            ->with('id', $id)
            ->with('name', $name);

    }

    public function description(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $description = $manga->getDescription();

        return view('edit.manga.information.description')
            ->with('id', $id)
            ->with('name', $name)
            ->with('description', $description);
    }

    public function type(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $type = $manga->getType();

        return view('edit.manga.information.type')
            ->with('id', $id)
            ->with('name', $name)
            ->with('type', $type);
    }

    public function names(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $assoc_names = $manga->getAssociatedNames();

        return view('edit.manga.information.names')
            ->with('id', $id)
            ->with('name', $name)
            ->with('assoc_names', $assoc_names);
    }

    public function genres(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $genres = $manga->getGenres();

        return view('edit.manga.information.genres')
            ->with('id', $id)
            ->with('name', $name)
            ->with('genres', $genres);
    }

    public function authors(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $authors = $manga->getAuthors();

        return view('edit.manga.information.authors')
            ->with('id', $id)
            ->with('name', $name)
            ->with('authors', $authors);
    }

    public function artists(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $artists = $manga->getArtists();

        return view('edit.manga.information.artists')
            ->with('id', $id)
            ->with('name', $name)
            ->with('artists', $artists);
    }

    public function year(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $year = $manga->getYear();

        return view('edit.manga.information.year')
            ->with('id', $id)
            ->with('name', $name)
            ->with('year', $year);
    }

    public function covers(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $archives = $manga->getArchives();

        return view('edit.manga.covers')
            ->with('id', $id)
            ->with('name', $name)
            ->with('archives', $archives);
    }
}
