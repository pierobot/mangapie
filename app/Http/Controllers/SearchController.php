<?php

namespace App\Http\Controllers;

use App\Http\Requests\Search\SearchAdvancedRequest;
use App\Http\Requests\Search\SearchAutoCompleteRequest;
use App\Http\Requests\Search\SearchRequest;
use Illuminate\Pagination\LengthAwarePaginator;

use \App\Manga;
use \App\LibraryPrivilege;

class SearchController extends Controller
{
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

        return view('search.index');
    }

    private function doBasicSearch($keywords)
    {
        // if the query is empty, then redirect to the advanced search page
        if ($keywords == null)
            return \Redirect::action('SearchController@index');

        $libraryIds = LibraryPrivilege::getIds();

        $results = Manga::search($keywords)
            ->filter(function ($manga) use ($libraryIds) {
                return in_array($manga->library->id, $libraryIds);
            })
            ->sortBy('name');

        $currentPage = \Input::get('page', 1);
        $mangaList = new LengthAwarePaginator($results->forPage($currentPage, 18), $results->count(), 18);
        $mangaList->onEachSide(1)
            ->withPath(\Request::getBaseUrl())
            ->appends([
                'type' => 'basic',
                'keywords' => $keywords
            ]);

        return view('home.index')
            ->with('header', 'Search Results (' . $mangaList->total() . ')')
            ->with('manga_list', $mangaList);
    }

    private function doAdvancedSearch($genres, $author, $artist, $keywords)
    {
        $results = Manga::advancedSearch($genres, $author, $artist, $keywords)->sortBy('name');
        $total = $results->count();

        $current_page = \Input::get('page', 1);
        $mangaList = new LengthAwarePaginator($results->forPage($current_page, 18), $total, 18);

        $mangaList = $mangaList->onEachSide(1)
            ->withPath(\Request::getBaseUrl())
            ->appends([
                'type' => 'advanced',
                'keywords' => $keywords,
                'genres' => $genres,
                'author' => $author,
                'artist' => $artist
            ]);

        return view('home.advancedsearch')
            ->with('header', 'Search Results (' . $total . ')')
            ->with('manga_list', $mangaList);
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
        $library_ids = LibraryPrivilege::getIds();

        $results = Manga::where('name', 'like', '%' . $query . '%')->get();
//        $assocResults = AssociatedName::where('name', 'like', '%' . $query . '%')->get();
//
//        $assocArray = [];
//        foreach ($assocResults as $assocName) {
//            array_push($assocArray, $assocName->reference->manga);
//        }
//
//        $results = $results->merge(collect($assocArray));

        if ($user->hasRole('Administrator') == false)
            $results = $results->whereIn('library_id', $library_ids);

        $results = $results->all();

        $array = [];
        foreach ($results as $manga) {
            array_push($array, [
                'id' => $manga->id,
                'name' => $manga->name
            ]);
        }

        return \Response::json($array);
    }
}
