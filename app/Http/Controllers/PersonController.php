<?php

namespace App\Http\Controllers;

use App\LibraryPrivilege;
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
        $page = request()->get('page');

        $results = $person->manga()->filter(function (Manga $manga) {
            return \Auth::user()->can('view', $manga->library);
        });

        $manga_list = new LengthAwarePaginator($results->forPage($page, 18), $results->count(), 18);
        $manga_list->withPath(request()->getBaseUrl());

        return view('home.person')
            ->with('header', 'Person: ' . $person->name . ' (' . $results->count() . ')')
            ->with('person', $person)
            ->with('manga_list', $manga_list);
    }

    // TODO: Add other resource methods
}
