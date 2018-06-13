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

        // determine whether or not the manga has been favorited
        $user_id = \Auth::user()->getId();
        $favorite = Favorite::where('user_id', $user_id)
                            ->where('manga_id', $id)
                            ->get();
        $is_favorited = $favorite->count() != 0;
        $isWatching = \Auth::user()->watchReferences->where('manga_id', $id)->first();

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
                                  ->with('path', $path)
                                  ->with('sort', $sort);
    }
}
