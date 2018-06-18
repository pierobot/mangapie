<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\StreamedResponse;

use \App\Manga;
use \App\Library;
use \App\LibraryPrivilege;
use \App\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        if ($user->isAdmin()) {
            $mangaList = Manga::orderBy('name', 'asc')
                              ->paginate(18);
        } else {
            $libraryIds = [];
            $user->privileges->each(function (LibraryPrivilege $privilege) use (&$libraryIds) {
                $libraryIds[] = $privilege->getLibraryId();
            });
            
            $mangaList = Manga::whereIn('library_id', $libraryIds)
                              ->orderBy('name', 'asc')
                              ->paginate(18);
        }

        $mangaList->withPath(\Config::get('app.url'));

        return view('home.index')->with('manga_list', $mangaList);
    }
}
