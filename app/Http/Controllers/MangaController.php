<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Carbon\Carbon;

use \App\Favorite;
use \App\Genre;
use \App\GenreInformation;
use \App\Manga;
use \App\MangaUpdates;
use App\WatchReference;

class MangaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Manga $manga, $sort = 'ascending')
    {
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
        ]);

        $user = \Auth::user()->load('favorites', 'readerHistory', 'watchReferences');

        return view('manga.index')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('sort', $sort);
    }

    public function files(Manga $manga)
    {
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
        ]);

        $user = \Auth::user()->load('favorites', 'readerHistory', 'watchReferences');

        return view('manga.files')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('sort', 'ascending');
    }

    public function comments(Manga $manga)
    {
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
        ]);

        $user = \Auth::user()->load('favorites', 'readerHistory', 'watchReferences');

        return view('manga.comments')
            ->with('user', $user)
            ->with('manga', $manga);
    }
}
