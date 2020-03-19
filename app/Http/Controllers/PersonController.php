<?php

namespace App\Http\Controllers;

use App\Manga;
use App\Person;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Person::class, 'person');
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function show(Person $person)
    {
        $request = request();
        $sort = $request->input('sort', 'asc');
        $perPage = 18;

        $libraries = $request->user()->libraries()->toArray();
        /** @var LengthAwarePaginator $items */
        $items = $person->manga()->whereIn('library_id', $libraries)
            ->orderBy('name', 'asc')
            ->with([
                'authors',
                'artists',
                'favorites',
                'votes'
            ])
            ->paginate($perPage, ['id', 'name'])
            ->appends($request->input());

        return view('home.person')
            ->with('header', 'Person: ' . $person->name . ' (' . $items->total() . ')')
            ->with('person', $person)
            ->with('manga_list', $items)
            ->with('sort', $sort);
    }

    // TODO: Add other resource methods
}
