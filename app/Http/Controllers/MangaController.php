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
        $id = $manga->getId();
        $name = $manga->getName();
        $path = $manga->getPath();

        $manga->load('archives', 'associatedNameReferences', 'genreReferences', 'authorReferences', 'artistReferences');

        $archives = $manga->getArchives($sort);
        $mu_id = $manga->getMangaUpdatesId();
        $description = $manga->getDescription();
        $type = $manga->getType();
        $assoc_names = $manga->getAssociatedNames();
        $genres = $manga->getGenres();
        $authors = $manga->getAuthors();
        $artists = $manga->getArtists();
        $year = $manga->getYear();
        $lastUpdated = $manga->getLastUpdated();

        $user = \Auth::user()->load('favorites', 'readerHistory', 'watchReferences', 'watchNotifications');
        $is_favorited = $user->favorites->where('user_id', $user->getId())->first() !== null;
        $isWatching = $user->watchReferences->where('manga_id', $id)->first() !== null;
        $watchNotifications = $user->watchNotifications->where('manga_id', $id);
        $readerHistory = $user->readerHistory->where('manga_id', $id);

        return view('manga.index')->with('id', $id)
                                  ->with('mu_id', $mu_id)
                                  ->with('is_favorited', $is_favorited)
                                  ->with('isWatching', $isWatching)
                                  ->with('name', $name)
                                  ->with('description', $description)
                                  ->with('type', $type)
                                  ->with('assoc_names', $assoc_names)
                                  ->with('genres', $genres)
                                  ->with('authors', $authors)
                                  ->with('artists', $artists)
                                  ->with('year', $year)
                                  ->with('lastUpdated', $lastUpdated)
                                  ->with('archives', $archives)
                                  ->with('readerHistory', $readerHistory)
                                  ->with('watchNotifications', $watchNotifications)
                                  ->with('path', $path)
                                  ->with('sort', $sort);
    }
}
