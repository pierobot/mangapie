<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Http\Requests\Favorite\FavoriteAddRequest;

use App\IntlString;
use App\Manga;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
        $page = request()->get('page');
        $perPage = 18;

        /** @var Builder $favorites */
        $favorites = $user->favorites()
            ->with([
                'manga',
                'manga.favorites',
                'manga.votes',
                'manga.authorReferences',
                'manga.authorReferences.author'
            ]);

        if (! empty($library)) {
            $favorites = $favorites->whereHas('manga', function (Builder $query) use ($library) {
                $query->where('library_id', $library);
            });
        }

        /** @var Collection $collection */
        $collection = $favorites->get()->transform(function (Favorite $favorite) {
            return $favorite->manga;
        });

        $collection = $collection->sort(function (Manga $left, Manga $right) use ($sort) {
            if ($sort === 'asc') {
                return IntlString::strcmp($left->name, $right->name) > 0;
            } else {
                return IntlString::strcmp($left->name, $right->name) < 0;
            }
        });

        // TODO: Should favorites to a library one can no longer access be viewable?

        $manga_list = new LengthAwarePaginator($collection->forPage($page, $perPage), $collection->count(), $perPage,
        					Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
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
