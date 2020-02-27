<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;

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

        $items = $user->manga()->with([
            'favorites',
            'votes',
            'authors',
            'artists'
        ])
        ->paginate(18, ['id', 'name']);

        /** @var LengthAwarePaginator $items */
        $items = $items->withPath(\Config::get('app.url'));

        return view('home.index')->with('manga_list', $items);
    }
}
