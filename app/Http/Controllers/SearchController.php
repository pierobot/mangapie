<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Requests\Search\SearchAdvancedRequest;
use App\Http\Requests\Search\SearchRequest;
use App\Manga;
use App\User;

class SearchController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        // if the query parameter 'type' exists then we're being requested another page of a search
        if (request()->has('type')) {
            $type = request()->get('type');
            $keywords = request()->get('keywords');

            if ($type == 'basic') {
                return $this->doBasicSearch($keywords);
            } else if ($type == 'advanced') {

                $genres = \Input::get('genres');
                $author = \Input::get('author');
                $artist = \Input::get('artist');

                if (is_null($keywords)) {
                    $keywords = '';
                }

                if (is_null($genres)) {
                    $genres = [];
                }

                if (is_null($author)) {
                    $author = '';
                }

                if (is_null($artist)) {
                    $artist = '';
                }

                return $this->doAdvancedSearch($genres, $author, $artist, $keywords);
            }
        }

        return view('search.index');
    }

    /**
     * Perform a quick search on a series' name and associated names using the like operator.
     * Requires the url parameter 'query' to be present. (?query=xxx)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoComplete()
    {
        /** @var User $user */
        $user = request()->user();
        $libraries = $user->libraries()->toArray();
        $searchQuery = request()->get('query');

        /** @var Builder $items */
        $items = Manga::where('name', 'like', "%$searchQuery%");

        $items = $items->orWhereHas('associatedNames', function (Builder $query) use ($searchQuery) {
            $query->where('name', 'like', "%$searchQuery%");
        });

        // filter out the items the user cannot access
        $items = $items->whereIn('library_id', $libraries)->get(['id', 'name']);

        return response()->json($items);
    }

    /**
     * @param SearchRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function basic(SearchRequest $request)
    {
        $keywords = $request->get('keywords');

        return $this->doBasicSearch($keywords);
    }

    /**
     * @param SearchAdvancedRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function advanced(SearchAdvancedRequest $request)
    {
        $keywords = $request->get('keywords');
        $genres = $request->get('genres');
        $author = $request->get('author');
        $artist = $request->get('artist');

        if (is_null($keywords)) {
            $keywords = '';
        }

        if (is_null($genres)) {
            $genres = [];
        }

        if (is_null($author)) {
            $author = '';
        }

        if (is_null($artist)) {
            $artist = '';
        }

        return $this->doAdvancedSearch($genres, $author, $artist, $keywords);
    }

    /**
     * @param string|null $keywords
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    private function doBasicSearch($keywords)
    {
        // if the query is empty, then redirect to the advanced search page
        if ($keywords == null) {
            return \Redirect::action('SearchController@index');
        }

        /** @var User $user */
        $user = request()->user();
        $libraries = $user->libraries()->toArray();
        $perPage = 18;

        $items = Manga::search($keywords)
            ->whereIn('library_id', $libraries)
            ->orderBy('name', 'asc')
            ->with([
                'authors',
                'artists',
                'favorites',
                'votes'
            ])
            ->paginate($perPage);

        /** @var LengthAwarePaginator $items */
        $items = $items->onEachSide(1)
            ->withPath(request()->getBaseUrl())
            ->appends([
                'type' => 'basic',
                'keywords' => $keywords,
            ]);

        return view('home.index')
            ->with('header', 'Search Results (' . $items->total() . ')')
            ->with('manga_list', $items);
    }

    /**
     * @param array $genres
     * @param string $author
     * @param string $artist
     * @param string $keywords
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function doAdvancedSearch(array $genres, string $author, string $artist, string $keywords)
    {
        /** @var User $user */
        $user = request()->user();
        $libraries = $user->libraries()->toArray();
        $perPage = 18;

        $items = Manga::advancedSearch($genres, $author, $artist, $keywords)
            ->whereIn('library_id', $libraries)
            ->orderBy('name', 'asc')
            ->with([
                'authors',
                'artists',
                'favorites',
                'votes'
            ])
            ->paginate($perPage)
            ->appends(request()->input());

        /** @var LengthAwarePaginator $items */
        $items = $items->onEachSide(1)
            ->withPath(request()->getBaseUrl());

        return view('home.advancedsearch')
            ->with('header', 'Search Results (' . $items->total() . ')')
            ->with('manga_list', $items);
    }
}
