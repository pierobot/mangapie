<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\Favorite\FavoriteAddRequest;

use App\IntlString;
use App\Manga;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FavoriteController extends Controller
{

    /**
     * Gets the view for the favorites.
     * Do not perform resource based authorization.
     *
     * @return mixed
     */
    public function index()
    {
        $user = \Auth::user();
        $sort = request()->input('sort', 'asc');
        $library = request()->query('library');
        $perPage = 18;

        /** @var Collection $favorites */
        $favorites = $user->favorites
            ->loadMissing([
                'manga',
                'manga.favorites',
                'manga.votes',
                'manga.authorReferences',
                'manga.authorReferences.author']
            );

        $collection = $favorites->transform(function (Favorite $favorite) {
            return $favorite->manga;
        });
        $collection = $collection->filter(function (Manga $manga) use ($library) {
            return $manga->library->id == $library;
        });
        $collection = $collection->sort(function (Manga $left, Manga $right) {
            return IntlString::strcmp($left->name, $right->name);
        });

        // TODO: Should favorites to a library one can no longer access be viewable?

        $page = request()->get('page');
        $manga_list = new LengthAwarePaginator($collection->forPage($page, $perPage), $collection->count(), $perPage);
        $manga_list->withPath(request()->path());
        $manga_list->appends(request()->input());

        return view('favorites.index')
            ->with('manga_list', $manga_list)
            ->with('sort', $sort)
            ->with('header', 'Favorites: (' . $favorites->count() . ')')
            ->with('total', $favorites->count());
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

    public function destroy(Favorite $favorite)
    {
        $favorite->forceDelete();

        return \Redirect::back()->with('success', 'You have unfavorited this manga.');
    }
}
