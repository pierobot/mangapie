<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Manga;
use App\Genre;
use App\AssociatedName;
use App\AssociatedNameReference;
use App\Author;
use App\AuthorReference;
use App\Artist;
use App\ArtistReference;
use App\GenreInformation;
use App\Http\Requests\EditMangaRequest;
use App\MangaUpdates;

class MangaEditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        if (\Auth::user()->isAdmin() == false && \Auth::user()->isMaintainer() == false) {
            return view('error.403');
        }

        $manga = Manga::find($id);
        if ($manga == null) {
            return view('error.404');
        }

        $name = $manga->getName();
        $mu_id = $manga->getMangaUpdatesId();
        $description = $manga->getDescription();
        $type = $manga->getType();
        $assoc_names = $manga->getAssociatedNames();
        $genres = $manga->getGenres();
        $authors = $manga->getAuthors();
        $artists = $manga->getArtists();
        $year = $manga->getYear();

        $genreCollection = Genre::all();
        $availableGenres = [];
        foreach ($genreCollection as $genre) {
            array_push($availableGenres, $genre->getName());
        }

        $archives = $manga->getArchives('ascending');

        return view('manga.edit')->with('id', $id)
                                 ->with('mu_id', $mu_id)
                                 ->with('name', $name)
                                 ->with('description', $description)
                                 ->with('type', $type)
                                 ->with('assoc_names', $assoc_names)
                                 ->with('availableGenres', $availableGenres)
                                 ->with('genres', $genres)
                                 ->with('authors', $authors)
                                 ->with('artists', $artists)
                                 ->with('year', $year)
                                 ->with('archives', $archives);
    }

    public function update(EditMangaRequest $request)
    {
        $id = \Input::get('id');
        $action = \Input::get('action');
        $manga = Manga::findOrFail($id);

        if ($action == 'autofill') {
            $result = MangaUpdates::autofillFromId($manga, \Input::get('mu_id'));

            if ($result == false) {
                return \Redirect::action('MangaEditController@index')
                                ->withErrors(['autofill' => 'Unable to scrape mangaupdates for information.']);
            }

            \Session::flash('success', 'The information was autofilled.');
        } elseif ($action == 'description.update') {
            $manga->setDescription(\Input::get('description'));

            \Session::flash('success', 'The description was successfully updated.');
        } elseif ($action == 'description.delete') {
            $manga->deleteDescription();

            \Session::flash('success', 'The description was successfully deleted.');
        } elseif ($action == 'type.update') {
            $manga->setType(\Input::get('type'));

            \Session::flash('success', 'The type was successfully updated.');
        } elseif ($action == 'type.delete') {
            $manga->deleteType();

            \Session::flash('success', 'The type was successfully deleted.');
        } elseif ($action == 'assoc_name.add') {
            $manga->addAssociatedName(\Input::get('assoc_name'));

            \Session::flash('success', 'The associated name was successfully added.');
        } elseif ($action == 'assoc_name.delete') {
            $manga->deleteAssociatedName(\Input::get('assoc_name'));

            \Session::flash('success', 'The associated name was successfully deleted.');
        } elseif ($action == 'genre.add') {
            $manga->addGenre(\Input::get('genre'));

            \Session::flash('success', 'The genre was successfully added.');
        } elseif ($action == 'genre.delete') {
            $manga->deleteGenreReference(\Input::get('genre'));

            \Session::flash('success', 'The genre was successfully deleted.');
        } elseif ($action == 'author.add') {
            $manga->addAuthor(\Input::get('author'));

            \Session::flash('success', 'The author was successfully added.');
        } elseif ($action =='author.delete') {
            $manga->deleteAuthorReference(\Input::get('author'));

            \Session::flash('success', 'The author was successfully deleted.');
        } elseif ($action == 'artist.add') {
            $manga->addArtist(\Input::get('artist'));

            \Session::flash('success', 'The artist was successfully added.');
        } elseif ($action == 'artist.delete') {
            $manga->deleteArtistReference(\Input::get('artist'));

            \Session::flash('success', 'The artist was successfully deleted.');
        } elseif ($action == 'year.update') {
            $manga->setYear(\Input::get('year'));

            \Session::flash('success', 'The year was successfully updated.');
        } elseif ($action == 'year.delete') {
            $manga->deleteYear();

            \Session::flash('success', 'The year was successfully deleted.');
        }

        return \Redirect::action('MangaEditController@index', [$id]);
    }
}
