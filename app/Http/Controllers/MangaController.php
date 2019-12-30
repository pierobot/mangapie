<?php

namespace App\Http\Controllers;

use \App\Manga;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MangaController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Manga::class, 'manga');
    }

//    /**
//     * Get the map of resource methods to ability names.
//     *
//     * @return array
//     */
//    protected function resourceAbilityMap()
//    {
//        return [
//            'index' => 'viewAny',
//            'show' => 'view',
//            'create' => 'create',
//            'store' => 'create',
//            'edit' => 'update',
//            'update' => 'update',
//            'destroy' => 'delete',
//        ];
//    }

    public function show(Manga $manga, $sort = 'ascending')
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

        $user = \Auth::user()->loadMissing(['favorites', 'readerHistory', 'watchReferences']);

        return view('manga.show')
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

        $user = \Auth::user()->loadMissing(['favorites', 'readerHistory', 'watchReferences']);

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

        $user = \Auth::user()->loadMissing(['favorites', 'readerHistory', 'watchReferences']);

        return view('manga.comments')
            ->with('user', $user)
            ->with('manga', $manga);
    }
}
