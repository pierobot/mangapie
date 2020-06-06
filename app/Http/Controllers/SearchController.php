<?php

namespace App\Http\Controllers;

use App\AssociatedName;
use App\AssociatedNameReference;
use App\Library;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Manga;
use App\User;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    /**
     * Action to present the advanced search view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        return view('search.index');
    }

    /**
     * Action that handles the GET request for a basic search.
     * @note The method does not take any parameters as they are passed by query string.
     *
     * @return \Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getBasic()
    {
        $request = request();
        $this->validate($request, [
            'keywords' => 'string|required',
            'page' => 'integer',
            'sort' => 'string|in:asc,desc'
        ]);

        $keywords = $request->input('keywords', "");
        $sort = $request->input('sort', 'asc');

        /** @var User $user */
        $user = $request->user();
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
            ->paginate($perPage, ['id', 'name'])
            ->appends($request->input());

        return view('home.index')
            ->with('header', 'Search Results (' . $items->total() . ')')
            ->with('manga_list', $items)
            ->with('sort', $sort);
    }

    /**
     * Route to perform a redirect on a basic search request from POST to GET.
     *
     * @param Request $request
     * @param string $sort
     * @return RedirectResponse
     */
    public function postBasic(Request $request)
    {
        // Redirect to the search page if the keywords are empty, otherwise to the get route along with the input
        return empty($request->post('keywords')) ?
            redirect()->action('SearchController@index') :
            // TODO: Find out why passing the input as an action parameter works but not the withInput method
            redirect()->action('SearchController@getBasic', request()->except(['_token']))/*->withInput()*/;
    }

    /**
     * Action that handles the GET request for an advanced search.
     * @note The method does not take any parameters as they are passed by query string.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAdvanced()
    {
        $request = request();

        $this->validate($request, [
            'libraries' => 'array|nullable|required_without_all:genres,author,artist,keywords',
            'libraries.*' => 'integer|exists:libraries,id|can:view,\App\Library',
            'genres' => 'array|nullable|required_without_all:artist,author,keywords,libraries',
            'genres.*' => 'integer|exists:genres,id',
            'artist' => 'nullable|string|required_without_all:genres,author,keywords,libraries',
            'author' => 'nullable|string|required_without_all:genres,artist,keywords,libraries',
            'keywords' => 'nullable|string|required_without_all:genres,artist,author,libraries',
            'page' => 'integer',
            'sort' => 'string|in:asc,desc'
        ]);

        /** @var User $user */
        $user = $request->user();
        $perPage = 18;

        $libraries = $request->input('libraries', $user->libraries()->toArray());
        $genres = $request->input('genres', []);
        $author = $request->input('author', '');
        $artist = $request->input('artist', '');
        $keywords = $request->input('keywords', '');
        $sort = $request->input('sort', 'asc');

        $items = Manga::advancedSearch($genres, $author, $artist, $keywords)
            ->whereIn('library_id', $libraries)
            ->orderBy('name', $sort)
            ->with([
                'authors',
                'artists',
                'favorites',
                'votes'
            ])
            ->paginate($perPage, ['id', 'name'])
            ->appends($request->input());

        return view('home.advancedsearch')
            ->with('header', 'Search Results (' . $items->total() . ')')
            ->with('manga_list', $items)
            ->with('sort', $sort);
    }

    /**
     * Route to perform a redirect on an advanced search request from POST to GET.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     * @deprecated
     */
    public function postAdvanced(Request $request)
    {
        // TODO: Find out why passing the input as an action parameter works but not the withInput method
        return redirect()->action('SearchController@getAdvanced', $request->except(['type', '_token']))/*->withInput()*/;
    }

    /**
     * Perform a quick search on a series' name and associated names using the like operator.
     * Requires the url parameter 'query' to be present. (?query=xxx)
     *
     * TODO: Is there a way to optimize the queries in this method?
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoComplete()
    {
        /** @var User $user */
        $user = request()->user();
        $libraries = $user->libraries()->toArray();
        $searchQuery = request()->get('query');

        /** @var Collection $items */
        $items = Manga::query()
            ->where('name', 'like', "%$searchQuery%")
            ->get(['id', 'name']);

        $associatedNameQuery = AssociatedNameReference::query()
            ->whereHas('associatedName', function (Builder $query) use ($searchQuery) {
                $query->select(['id'])
                      ->where('name', 'like', "%$searchQuery%");
            })
            ->select(['manga_id', 'associated_name_id']);

        $associatedItems = Manga::query()
            ->joinSub($associatedNameQuery, 'associatedNameReferences', function (JoinClause $join) {
                $join->on('manga.id', '=', 'manga_id');
            })
            ->join('associated_names', function (JoinClause $join) {
                $join->on('associated_name_id', '=', 'associated_names.id');
            })
            ->select(['manga.id', 'associated_names.name'])
            ->get();

        return response()->json($items->merge($associatedItems));
    }
}
