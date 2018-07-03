<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Artist;
use App\ArtistReference;
use App\Library;
use App\LibraryPrivilege;

class ArtistController extends Controller
{
    public function index(Artist $artist)
    {
        $page = \Input::get('page');

        $libraryIds = LibraryPrivilege::getIds();

        $references = ArtistReference::where('artist_id', $artist->getId())
                                     ->get()
                                     ->filter(function ($reference) use ($libraryIds) {
                                         foreach ($libraryIds as $libraryId) {
                                             return $reference->manga->getLibraryId() == $libraryId;
                                         }
                                     });

        $results = [];
        foreach ($references as $reference) {
            if ($reference->manga->getLibraryId())
                $results[] = $reference->manga;
        }

        $results = collect($results)->sortBy('name');

        $manga_list = new LengthAwarePaginator($results->forPage($page, 18), $results->count(), 18);
        $manga_list->withPath(\Request::getBaseUrl());

        return view('home.artist')
            ->with('header', 'Artist: ' . $artist->getName() . ' (' . $results->count() . ')')
            ->with('artist', $artist)
            ->with('manga_list', $manga_list);
    }
}
