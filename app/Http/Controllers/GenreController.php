<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Genre;
use App\GenreReference;
use App\Library;
use App\LibraryPrivilege;

class GenreController extends Controller
{
    public function index(Genre $genre)
    {
        $page = \Input::get('page');

        $libraryIds = LibraryPrivilege::getIds();

        $references = GenreReference::where('genre_id', $genre->getId())
                                    ->get()
                                    ->filter(function ($reference) use ($libraryIds) {
                                        foreach ($libraryIds as $libraryId) {
                                            return $reference->manga->getLibraryId() == $libraryId;
                                        }
                                    });

        $results = [];
        foreach ($references as $reference) {
            if ($reference->manga->getLibraryId())
                array_push($results, $reference->manga);
        }

        $results = collect($results)->sortBy('name');

        $manga_list = new LengthAwarePaginator($results->forPage($page, 18), $results->count(), 18);
        $manga_list->withPath(\Request::getBaseUrl());

        return view('home.index')->with('header', 'Genre: ' . $genre->getName() . ' (' . $results->count() . ')')
                                 ->with('manga_list', $manga_list);
    }
}
