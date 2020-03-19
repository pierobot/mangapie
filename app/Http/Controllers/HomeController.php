<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    /**
     * Gets the view for the home page.
     *
     * @param string $sort
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = \Auth::user();
        $sort = request()->input('sort', 'asc');
        $perPage = 18;

        $items = $user->manga()
            ->orderBy('name', $sort)
            ->with([
                'favorites',
                'votes',
                'authors',
                'artists'
            ])
            ->paginate($perPage, ['id', 'name'])
            ->appends(request()->input());

        return view('home.index')
            ->with('manga_list', $items)
            ->with('sort', $sort);
    }
}
