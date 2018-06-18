<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\FavoriteRequest;
use App\Library;
use App\LibraryPrivilege;
use App\Manga;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $total = $user->favorites->count();
        $favoriteIds = [];

        $user->favorites->each(function (Favorite $favorite) use (&$favoriteIds) {
            $favoriteIds[] = $favorite->manga->getId();
        });

        $favoriteList = Manga::whereIn('id', $favoriteIds)
                             ->orderBy('name', 'asc')
                             ->paginate(18);

        $favoriteList->withPath(\Config::get('app.url'));

        return view('favorites.index')->with('manga_list', $favoriteList)
                                      ->with('header', 'Favorites: (' . $total . ')')
                                      ->with('total', $total);
    }

    public function update(FavoriteRequest $request)
    {
        $id = intval(\Input::get('id'));
        $user_id = \Auth::user()->getId();
        $action = \Input::get('action');

        if ($action == 'favorite') {
            $favorite = Favorite::updateOrCreate([
                'user_id' => $user_id,
                'manga_id' => $id
            ]);

            \Session::flash('success', 'You have favorited this manga.');
        } else {
            $favorite = Favorite::where('user_id', $user_id)
                                ->where('manga_id', $id);

            $favorite->forceDelete();

            \Session::flash('success', 'You have unfavorited this manga.');
        }

        return \Redirect::action('MangaController@index', [$id]);
    }
}
