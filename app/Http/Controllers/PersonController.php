<?php

namespace App\Http\Controllers;

use App\LibraryPrivilege;
use App\Manga;
use App\Person;

use Illuminate\Pagination\LengthAwarePaginator;

class PersonController extends Controller
{
    public function index(Person $person)
    {
        $page = request()->get('page');

        $libraryIds = LibraryPrivilege::getIds();

        $results = $person->manga()->filter(function (Manga $manga) use ($libraryIds) {
            return in_array($manga->library->id, $libraryIds);
        });

        $manga_list = new LengthAwarePaginator($results->forPage($page, 18), $results->count(), 18);
        $manga_list->withPath(request()->getBaseUrl());

        return view('home.person')
            ->with('header', 'Person: ' . $person->getName() . ' (' . $results->count() . ')')
            ->with('person', $person)
            ->with('manga_list', $manga_list);
    }
}
