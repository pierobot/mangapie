<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Genre;
use App\Manga;
use App\User;

class GenreController extends Controller
{
    /**
     * @param Genre $genre
     * @return mixed
     */
    public function index(Genre $genre)
    {
        /** @var User $user */
        $user = request()->user();
        $libraries = $user->libraries()->toArray();
        $perPage = 18;
        $name = $genre->name;

        /** @var Builder $items */
        $items = Manga::whereHas('genres', function (Builder $query) use ($name) {
            $query->where('name', $name);
        });

        // filter out the items the user cannot access
        $items = $items->whereIn('library_id', $libraries)
            ->orderBy('name', 'asc')
            ->with([
                'authors',
                'artists',
                'favorites',
                'votes'
            ])
            ->paginate($perPage);

        /** @var LengthAwarePaginator $items */
        $items = $items->withPath(\Config::get('app.url'));

        return view('home.genre')
            ->with('header', 'Genre: ' . $name . ' (' . $items->total() . ')')
            ->with('genre', $genre)
            ->with('manga_list', $items);
    }
}
