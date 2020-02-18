<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
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

        $page = request()->get('page');

        /** @var Builder $items */
        $items = $user->manga()->with([
            'favorites',
            'votes',
            'authorReferences',
            'authorReferences.author'
        ]);

        $items = $items->paginate(18, ['id', 'name'], 'page', $page);
        /** @var LengthAwarePaginator $items */
        $items = $items->withPath(\Config::get('app.url'));

        return view('home.index')->with('manga_list', $items);
    }
}
