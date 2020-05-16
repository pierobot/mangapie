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
        $filter = request()->query('filter', 'Root');

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

        $topMostDirectories = $manga->topMostDirectories();
        /** @var Collection $items */
        $items = $manga->archives()
            ->orderBy('name', $sort)
            ->get();

        $rootItems = $items->filter(function (Archive $archive) {
            $fileInfo = new SplFileInfo($archive->name);
            $basePath = $fileInfo->getPath();

            return empty($basePath);
        });

        if ($rootItems->count()) {
            if (count($topMostDirectories)) {
                // Only add Root to the directories if it has files and if there are other directories
                $topMostDirectories = array_merge(['Root'], $topMostDirectories);
            }
        } else {
            if ($filter === 'Root') {
                $filter = count($topMostDirectories) ? $topMostDirectories[0] : null;
            }
        }

        if ($filter !== 'Root') {
            $items = $items->filter(function (Archive $archive) use ($filter) {
                $fileInfo = new SplFileInfo($archive->name);
                $basePath = $fileInfo->getPath();

                return $basePath == $filter;
            });
        } else {
            $items = $rootItems;
        }

        return view('manga.show')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('items', $items)
            ->with('topMostDirectories', $topMostDirectories)
            ->with('sort', $sort)
            ->with('filter', $filter);
    }

    public function files(Manga $manga)
    {
        $sort = request()->query('sort', 'asc');
        $filter = request()->query('filter', 'Root');

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

        $topMostDirectories = $manga->topMostDirectories();
        /** @var Collection $items */
        $items = $manga->archives()
            ->orderBy('name', $sort)
            ->get();

        $rootItems = $items->filter(function (Archive $archive) {
            $fileInfo = new SplFileInfo($archive->name);
            $basePath = $fileInfo->getPath();

            return empty($basePath);
        });

        if ($rootItems->count()) {
            if (count($topMostDirectories)) {
                // Only add Root to the directories if it has files and if there are other directories
                $topMostDirectories = array_merge(['Root'], $topMostDirectories);
            }
        } else {
            if ($filter === 'Root') {
                $filter = count($topMostDirectories) ? $topMostDirectories[0] : null;
            }
        }

        if ($filter !== 'Root') {
            $items = $items->filter(function (Archive $archive) use ($filter) {
                $fileInfo = new SplFileInfo($archive->name);
                $basePath = $fileInfo->getPath();

                return $basePath == $filter;
            });
        } else {
            $items = $rootItems;
        }

        return view('manga.files')
            ->with('user', $user)
            ->with('manga', $manga)
            ->with('items', $items)
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
