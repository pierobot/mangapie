<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Library;
use App\LibraryPrivilege;
use App\Manga;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = \Auth::user();
        $libraries = null;

        $ids = [];
        $favorites = Favorite::where('user_id', $user->getId())->get();
        foreach ($favorites as $favorite) {
            array_push($ids, $favorite->getMangaId());
        }

        if ($user->isAdmin() == true) {
            $libraries = Library::all();
        } else {
            $library_ids = LibraryPrivilege::getIds($user->getId());
            $libraries = Library::whereIn('id', $library_ids)->get();
        }

        $favorite_list = Manga::whereIn('id', $ids)->orderBy('name', 'asc')->paginate(18);
        $total = count($favorites);

        return view('manga.favorites', compact('favorite_list', 'libraries', 'total'));
    }

    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer',
            'action' => ['required', 'regex:/favorite|unfavorite/']
        ]);

        $id = intval(\Input::get('id'));
        if ($validator->fails()) {
            return \Redirect::action('MangaInformationController@index', [$id])
                            ->withErrors($validator, 'update');
        }

        $user_id = \Auth::user()->getId();
        $action = \Input::get('action');

        if ($action == 'favorite') {
            $favorite = Favorite::updateOrCreate([
                'user_id' => $user_id,
                'manga_id' => $id
            ]);

            \Session::flash('favorite-success', 'You have favorited this manga.');
        } else {
            $favorite = Favorite::where('user_id', $user_id)
                                ->where('manga_id', $id);

            $favorite->forceDelete();

            \Session::flash('favorite-success', 'You have unfavorited this manga.');
        }

        return \Redirect::action('MangaInformationController@index', [$id]);
    }
}
