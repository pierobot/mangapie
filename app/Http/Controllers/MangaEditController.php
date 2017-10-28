<?php

namespace App\Http\Controllers;

use App\AssociatedName;
use App\AssociatedNameReference;
use App\Author;
use App\AuthorReference;
use App\Artist;
use App\ArtistReference;
use App\GenreInformation;
use App\Http\Requests\EditMangaRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

use \App\Manga;
use \App\MangaInformation;
use \App\Genre;

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
        $info = MangaInformation::find($id);

        $mu_id = null;
        $description = null;
        $type = null;
        $assoc_names = null;
        $genres = null;
        $authors = null;
        $artists = null;
        $year = null;
        if ($info != null) {
            $mu_id = $info->getMangaUpdatesId();
            $description = $info->getDescription();
            $type = $info->getType();
            $assoc_names = $info->getAssociatedNames();
            $genres = $info->getGenres();
            $authors = $info->getAuthors();
            $artists = $info->getArtists();
            $year = $info->getYear();
        }

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
        $information = MangaInformation::firstOrNew(['id' => $id]);

        if ($action == 'description.update') {
            $information->updateDescription(\Input::get('description'));

            \Session::flash('update-success', 'The description was successfully updated.');
        } elseif ($action == 'description.delete') {
            $information->deleteDescription();

            \Session::flash('update-success', 'The description was successfully deleted.');
        } elseif ($action == 'type.update') {
            $information->updateType(\Input::get('type'));

            \Session::flash('update-success', 'The type was successfully updated.');
        } elseif ($action == 'type.delete') {
            $information->deleteType();

            \Session::flash('update-success', 'The type was successfully deleted.');
        } elseif ($action == 'assoc_name.add') {
            $information->addAssociatedName(\Input::get('assoc_name'));

            \Session::flash('update-success', 'The associated name was successfully added.');
        } elseif ($action == 'assoc_name.delete') {
            $information->deleteAssociatedName(\Input::get('assoc_name'));

            \Session::flash('update-success', 'The associated name was successfully deleted.');
        } elseif ($action == 'genre.add') {
            $information->addGenre(\Input::get('genre'));

            \Session::flash('update-success', 'The genre was successfully added.');
        } elseif ($action == 'genre.delete') {
            $information->deleteGenre(\Input::get('genre'));

            \Session::flash('update-success', 'The genre was successfully deleted.');
        } elseif ($action == 'author.add') {
            $information->addAuthor(\Input::get('author'));

            \Session::flash('update-success', 'The author was successfully added.');
        } elseif ($action =='author.delete') {
            $information->deleteAuthor(\Input::get('author'));

            \Session::flash('update-success', 'The author was successfully deleted.');
        } elseif ($action == 'artist.add') {
            $information->addArtist(\Input::get('artist'));

            \Session::flash('update-success', 'The artist was successfully added.');
        } elseif ($action == 'artist.delete') {
            $information->deleteArtist(\Input::get('artist'));

            \Session::flash('update-success', 'The artist was successfully deleted.');
        } elseif ($action == 'year.update') {
            $information->updateYear(\Input::get('year'));

            \Session::flash('update-success', 'The year was successfully updated.');
        } elseif ($action == 'year.delete') {
            $information->deleteYear();

            \Session::flash('update-success', 'The year was successfully deleted.');
        }

        $information->save();

        return \Redirect::action('MangaEditController@index', [$id]);
    }
}
