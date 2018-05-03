<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Author;
use App\AuthorReference;
use App\Library;
use App\LibraryPrivilege;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Author $author)
    {
        $page = \Input::get('page');

        $libraryIds = LibraryPrivilege::getIds(\Auth::user()->getId());
        $libraries = Library::whereIn('id', $libraryIds)->get();

        $references = AuthorReference::where('author_id', $author->getId())
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

        return view('home.index')->with('header', 'Author: ' . $author->getName() . ' (' . $results->count() . ')')
                                 ->with('manga_list', $manga_list)
                                 ->with('libraries', $libraries);
    }
}
