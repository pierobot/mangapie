<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchAdvancedRequest;
use App\Http\Requests\SearchAutoCompleteRequest;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use \App\Genre;
use \App\Manga;
use \App\Library;
use \App\LibraryPrivilege;

class SearchController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // if the query parameter 'type' exists then we're being requested another page of a search
        if (\Input::has('type')) {
            $type = \Input::get('type');
            $keywords = \Input::get('keywords');

            if ($type == 'basic') {
                return $this->doBasicSearch($keywords);
            } else if ($type == 'advanced') {

                $genres = \Input::get('genres');
                $author = \Input::get('author');
                $artist = \Input::get('artist');

                return $this->doAdvancedSearch($genres, $author, $artist, $keywords);
            }
        }

        $genres = Genre::all();

        return view('search.index')->with('genres', $genres);
    }

    private function doBasicSearch($keywords)
    {
        // if the query is empty, then redirect to the advanced search page
        if ($keywords == null)
            return \Redirect::action('SearchController@index');

        $user = \Auth::user();
        $library_ids = LibraryPrivilege::getIds($user->getId());
        $libraries = null;

        if ($user->isAdmin() == true) {
            $manga_list = Manga::search($keywords)
                               ->orderBy('name', 'asc')
                               ->paginate(18);
            $libraries = Library::all();
        } else {
            $manga_list = Manga::search($keywords)
                               ->whereIn('library_id', $library_ids)
                               ->orderBy('name', 'asc')
                               ->paginate(18);

            $libraries = Library::whereIn('id', $library_ids)->get();
        }

        $manga_list->withPath(\Request::getBaseUrl())
                   ->appends([
                       'type' => 'basic',
                       'keywords' => $keywords
                   ]);

        return view('home.index')->with('header', 'Search Results (' . $manga_list->total() . ')')
                                 ->with('manga_list', $manga_list)
                                 ->with('libraries', $libraries);
    }

    private function doAdvancedSearch($genres, $author, $artist, $keywords)
    {
        $results = Manga::advancedSearch($genres, $author, $artist, $keywords);
        $total = $results->count();

        $libraryIds = LibraryPrivilege::getIds(\Auth::user()->getId());
        $libraries = Library::whereIn('id', $libraryIds)->get();

        $current_page = \Input::get('page', 1);
        $manga_list = new LengthAwarePaginator($results->forPage($current_page, 18), $total, 18);

        $manga_list = $manga_list->withPath(\Request::getBaseUrl())
                                 ->appends([
                                     'type' => 'advanced',
                                     'keywords' => $keywords,
                                     'genres' => $genres,
                                     'author' => $author,
                                     'artist' => $artist
                                 ]);

        return view('home.index')->with('header', 'Search Results (' . $total . ')')
                                 ->with('manga_list', $manga_list)
                                 ->with('libraries', $libraries);
    }

    public function basic(SearchRequest $request)
    {
        $keywords = \Input::get('keywords');

        return $this->doBasicSearch($keywords);
    }

    public function advanced(SearchAdvancedRequest $request)
    {
        $keywords = \Input::get('keywords');
        $genres = \Input::get('genres');
        $author = \Input::get('author');
        $artist = \Input::get('artist');

        return $this->doAdvancedSearch($genres, $author, $artist, $keywords);
    }

    public function autoComplete()
    {
        $query = \Input::get('query');

        $user = \Auth::user();
        $library_ids = LibraryPrivilege::getIds($user->getId());
        $libraries = null;

        if ($user->isAdmin() == true) {
            $manga_list = Manga::where('name', 'like', '%' . $query . '%')
                               ->orderBy('name', 'asc')
                               ->get();
        } else {
            $manga_list = Manga::where('name', 'like', '%' . $query . '%')
                               ->whereIn('library_id', $library_ids)
                               ->orderBy('name', 'asc')
                               ->get();
        }

        $results = [];
        foreach ($manga_list as $manga) {
            array_push($results, [
                'id' => $manga->getId(),
                'name' => $manga->getName()
            ]);
        }

        return \Response::json($results);
    }
}
