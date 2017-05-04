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

class MangaController extends Controller
{
    //public $perPage = 25;

    public function __construct() {
        $this->middleware('auth');

        if (Manga::count() == 0) {
            if (Library::count() == 0) {
                foreach (\Config::get('mangapie.libraries') as $library) {
                    Library::create([
                        'name' => $library['name'],
                        'path' => $library['path']
                    ]);
                }
            }

            $libraries = Library::all();

            // Populate the manga in each library path
            foreach ($libraries as $library) {
                foreach (\File::directories($library['path']) as $path) {
                    $manga = Manga::create([
                        'name' => pathinfo($path, PATHINFO_FILENAME),
                        'path' => $path,
                        'library_id' => Library::where('name','=',$library->name)->first()->id
                    ]);
                }
            }
        }
    }

    public function index() {
        $user = \Auth::user();
        $libraries = null;

        if ($user->isAdmin() == true) {
            $libraries = Library::all();
            $manga_list = Manga::orderBy('name', 'asc')->get();
        } else {
            $library_ids = LibraryPrivilege::getIds($user->getId());

            $manga_list = Manga::whereIn('library_id', $library_ids)->orderBy('name', 'asc')->get();
            $libraries = Library::whereIn('id', $library_ids)->get();
        }
 
        return view('manga.index', compact('manga_list', 'libraries'));
    }

    public function library($id) {
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
            $manga_list = Manga::where('library_id', '=', $id)->get();

            if ($user->isAdmin() == true) {
                $libraries = Library::all();
            } else {
                $library_ids = LibraryPrivilege::getIds($user->getId());
                $libraries = Library::whereIn('id', $library_ids)->get();
            }
        }

        return $can_access == true ? view('manga.index', compact('manga_list', 'libraries')) :
                                     view('error.403');
    }

    public function thumbnail($id) {
        $manga = Manga::find($id);
        if ($manga !== null) {
            $thumbnail_path = $manga->path . "/folder.jpg";

            if (\File::exists($thumbnail_path) == false) {
                $thumbnail_path = $manga->path . "/folder.jpeg";

                if (\File::exists($thumbnail_path) == false) {
                    $thumbnail_path = "public/img/unknown.jpg";
                }
            }

            // $headers = [
            //     'Content-Type' => \File::mimeType($thumbnail_path),
            //     'Content-Length' => \File::size($thumbnail_path)
            // ];
            
            // return \Response::stream(function () use($thumbnail_path) {
            //         if ($file = fopen($thumbnail_path, 'rb')) {
            //             fpassthru($file);
            //             flush();
            //             fclose($file);
            //         }
            //     },
            //     200,
            //     $headers
            // );

            //return $response;

            //return file_get_contents($thumbnail_path);
            return response()->file($thumbnail_path);
        }

        return response('', 404);
    }

    public function show(Paginator $paginator) {

    }
}
