<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;

use \App\Library;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Gets the view for the home page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = \Auth::user();

        $libraries = Library::all()->filter(function (Library $library) use ($user) {
            return $user->can('view', $library);
        });

        $collection = collect();

        $libraries = $libraries->loadMissing(
            'manga',
            'manga.favorites',
            'manga.votes',
            'manga.authorReferences',
            'manga.authorReferences.author');

        foreach ($libraries as $library) {
            $mangas = $library->manga;

            foreach ($mangas as $manga) {
                $collection->push($manga);
            }
        }

        $page = request()->get('page');
        $manga_list = new LengthAwarePaginator($collection->forPage($page, 18), $collection->count(), 18);
        $manga_list->withPath(\Config::get('app.url'));

        return view('home.index')->with('manga_list', $manga_list);
    }
}
