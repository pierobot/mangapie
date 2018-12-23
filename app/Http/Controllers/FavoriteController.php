<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\Favorite\FavoriteAddRequest;
use App\Http\Requests\Favorite\FavoriteRemoveRequest;
use App\Manga;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $favorites = $user->favorites->load('manga');
        $total = $favorites->count();
        $favoriteIds = [];

        $favorites->each(function (Favorite $favorite) use (&$favoriteIds) {
            $favoriteIds[] = $favorite->manga->getId();
        });

        $favoriteList = Manga::whereIn('id', $favoriteIds)
                             ->orderBy('name', 'asc')
                             ->paginate(18);

        $favoriteList->onEachSide(1)->withPath(\Config::get('app.url'));

        return view('favorites.index')
            ->with('manga_list', $favoriteList)
            ->with('header', 'Favorites: (' . $total . ')')
            ->with('total', $total);
    }

    public function create(FavoriteAddRequest $request)
    {
        $mangaId = intval($request->get('manga_id'));

        $favorite = Favorite::updateOrCreate([
            'user_id' => \Auth::id(),
            'manga_id' => $mangaId
        ]);

        session()->flash('success', 'You have favorited this manga.');

        return redirect()->back();
    }

    public function delete(FavoriteRemoveRequest $request)
    {
        $favorite = Favorite::find($request->get('favorite_id'));
        if ($favorite->user->id !== \Auth::id())
            return redirect()->back(403);

        $favorite->forceDelete();

        session()->flash('success', 'You have unfavorited this manga.');

        return redirect()->back();
    }
}
