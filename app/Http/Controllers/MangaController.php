<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Carbon\Carbon;

use \App\Favorite;
use \App\Genre;
use \App\GenreInformation;
use \App\Manga;
use \App\Sources\MangaUpdates;
use App\WatchReference;

class MangaController extends Controller
{
    public function index(Manga $manga, $sort = 'ascending')
    {
        // these are all required because of the responsive layouts
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
            'votes'
        ]);

        $user = auth()->user()->load(['favorites', 'readerHistory', 'watchReferences']);

        return view('manga.index')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('sort', $sort);
    }

    public function files(Manga $manga, $sort = 'ascending')
    {
        // these are all required because of the responsive layouts
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
            'votes'
        ]);

        $user = auth()->user()->load(['favorites', 'readerHistory', 'watchReferences']);

        return view('manga.files')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('sort', $sort);
    }

    public function comments(Manga $manga)
    {
        // these are all required because of the responsive layouts
        $manga = $manga->load([
            'archives',
            'associatedNameReferences.associatedName',
            'authorReferences.author',
            'artistReferences.artist',
            'genreReferences.genre',
            'comments',
            'votes'
        ]);

        $user = auth()->user()->load(['favorites', 'readerHistory', 'watchReferences']);

        return view('manga.comments')
            ->with('user', $user)
            ->with('manga', $manga);
    }
}
