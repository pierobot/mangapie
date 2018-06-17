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
        $libraries = null;

        $ids = [];
        $favorites = $user->favorites;
        foreach ($favorites as $favorite) {
            array_push($ids, $favorite->manga->getId());
        }

        if ($user->isAdmin() == true) {
            $libraries = Library::all();
        } else {
            $library_ids = LibraryPrivilege::getIds();
            $libraries = Library::whereIn('id', $library_ids)->get();
        }

        $favorite_list = Manga::whereIn('id', $ids)->orderBy('name', 'asc')->paginate(18);
        $total = count($favorites);

        $favorite_list->withPath(env('app.url'));

        return view('manga.favorites')->with('header', 'Favorites: (' . $total . ')')
                                      ->with('manga_list', $favorite_list)
                                      ->with('libraries', $libraries)
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
