<?php

namespace App\Http\Controllers;

use App\Archive;
use \App\Manga;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Collection;
use SplFileInfo;

class MangaController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Manga::class, 'manga');
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'show' => 'view',
            'files' => 'view',
        ];
    }

    public function show(Manga $manga)
    {
        $sort = request()->query('sort', 'asc');
        $filter = request()->query('filter');

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

        $sortByTopMostDirectories = true;
        $topMostDirectories = $manga->topMostDirectories();
        $topMostDirectory = ! empty($topMostDirectories) ? $topMostDirectories[0] : null;
        /** @var Collection $items */
        $items = $manga->archives()
            ->orderBy('name', $sort)
            ->get();

        if ($sortByTopMostDirectories && $topMostDirectory) {
            // If sorting by top most directories is enabled
            // then we'll need to filter by the first directory if there is no filter
            $filter = $filter ?? $topMostDirectory;

            $items = $items->filter(function (Archive $archive) use ($filter) {
                $fileInfo = new SplFileInfo($archive->name);
                $basePath = $fileInfo->getPath();

                return $basePath == $filter;
            });
        }

        return view('manga.show')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('items', $items)
            ->with('sortByTopMostDirectories', $sortByTopMostDirectories)
            ->with('topMostDirectories', $topMostDirectories)
            ->with('sort', $sort)
            ->with('filter', $filter);
    }

    public function files(Manga $manga)
    {
        $sort = request()->query('sort', 'asc');
        $filter = request()->query('filter');

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

        $sortByTopMostDirectories = true;
        $topMostDirectories = $manga->topMostDirectories();
        $topMostDirectory = ! empty($topMostDirectories) ? $topMostDirectories[0] : null;

        $items = $manga->archives()
            ->orderBy('name', $sort)
            ->get();

        if ($sortByTopMostDirectories && $topMostDirectory) {
            // If sorting by top most directories is enabled
            // then we'll need to filter by the first directory if there is no filter
            $filter = $filter ?? $topMostDirectory;

            $items = $items->filter(function (Archive $archive) use ($filter) {
                $fileInfo = new SplFileInfo($archive->name);
                $basePath = $fileInfo->getPath();

                return $basePath == $filter;
            });
        }

        return view('manga.files')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('items', $items)
            ->with('sortByTopMostDirectories', $sortByTopMostDirectories)
            ->with('topMostDirectories', $topMostDirectories)
            ->with('sort', $sort)
            ->with('filter', $filter);
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
