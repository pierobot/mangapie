<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $genres = Genre::all();

        return view('search.index', compact('genres'));
    }

    private function doBasicSearch($query)
    {
        // if the query is empty, then redirect to the advanced search page
        if ($query == null)
            return \Redirect::action('SearchController@index');

        $user = \Auth::user();
        $library_ids = LibraryPrivilege::getIds($user->getId());
        $libraries = null;

        if ($user->isAdmin() == true) {
            $manga_list = Manga::search($query)
                               ->orderBy('name', 'asc')
                               ->paginate(18);
            $libraries = Library::all();
        } else {
            $manga_list = Manga::search($query)->whereIn('library_id', $library_ids)
                               ->orderBy('name', 'asc')
                               ->paginate(18);

            $libraries = Library::whereIn('id', $library_ids)->get();
        }

        return view('manga.index', compact('manga_list', 'libraries'));
    }

    private function doAdvancedSearch($query, $genres)
    {

    }

    public function search(Request $request)
    {
        // Ensure the form data is valid
        $this->validate($request, [
            'type' => 'string|required',
            'query' => 'string|required_without:genres',
            'genres'=> 'array|required_without:query'
        ]);

        // dd($a);

        $query = \Input::get('query');
        $type = \Input::get('type');
        $genres = \Input::get('genres');

        if ($type == 'basic')
            return $this->doBasicSearch($query);
        elseif ($type == 'advanced') {
            return $this->doAdvancedSearch($query, $genres);
        }

        return \Redirect::action('SearchController@index');
    }
}
