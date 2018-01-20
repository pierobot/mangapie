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
    //public $perPage = 25;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = \Auth::user();
        $libraries = null;

        if ($user->isAdmin() == true) {
            $libraries = Library::all();

            $manga_list = Manga::orderBy('name', 'asc')->paginate(18);
        } else {
            $library_ids = LibraryPrivilege::getIds($user->getId());

            $manga_list = Manga::whereIn('library_id', $library_ids)->orderBy('name', 'asc')->paginate(18);
            $libraries = Library::whereIn('id', $library_ids)->get();
        }

        $manga_list->withPath(env('app.url'));

        return view('home.index', compact('manga_list', 'libraries'));
    }

    public function library($id)
    {
        $user = \Auth::user();
        $can_access = false;

        // check if the user has sufficient privileges
        if ($user->isAdmin() == true) {
            // admin can always access everything
            $can_access = true;
        } else {
            $privileges = LibraryPrivilege::where('user_id', '=', $user->getId())->get();
            // a regular user needs to have library privileges
            foreach ($privileges as $privilege) {
                if ($privilege->getLibraryId() == $id) {
                    $can_access = true;
                    break;
                }
            }
        }

        $manga_list = null;
        $libraries = null;
        if ($can_access == true) {
            $manga_list = Manga::where('library_id', '=', $id)->orderBy('name', 'asc')->paginate(18);

            if ($user->isAdmin() == true) {
                $libraries = Library::all();
            } else {
                $library_ids = LibraryPrivilege::getIds($user->getId());
                $libraries = Library::whereIn('id', $library_ids)->get();
            }
        }

        $manga_list->withPath(env('app.url'));

        return $can_access == true ? view('home.index', compact('manga_list', 'libraries')) :
                                     view('error.403');
    }
}
