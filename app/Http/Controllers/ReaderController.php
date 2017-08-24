<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Symfony\Component\Finder\Finder;

use \App\Manga;
use \App\Library;

class ReaderController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    //
    public function index($id, $archive_name, $page = 1) {
        $manga = Manga::find($id);
        // This controller/view implements a custom navbar
        $custom_navbar = true;
        // get the archive path
        $matches = Finder::create()->in($manga->getPath())->name($archive_name);

        if (count($matches) == 0) {
            \Session::flash('reader-failure', 'Unable to find ' . "'" . $archive_name . "'");

            return view('manga.reader');
        }

        // ensure we don't have any archives with the same name.
        // TO-DO: THIS WILL BE ALTERED IN THE FUTURE TO ALLOW SAME FILE NAMES
        //        SO LONG AS THEY ARE IN A DIFFERENT SUBDIRECTORY.
        if (count($matches) > 1) {

            \Session::flash('reader-failure', 'The archive ' . "'" . $archive_name . "'" . 'has the same name as another. This is a known bug.');

            return view('manga.reader');
        }
        
        // get the path of the first. 
        // this is bugged, see above.
        // TO-DO: will fix later as this will also require changes to reader.blade.php
        $archive_path = '';
        foreach ($matches as $match) {

            $archive_path = $match->getPathname();
            break;
        }

        // Get the image count
        $page_count = $manga->getImageCount($archive_path);

        $prev_url = false;
        $next_url = false;
        $app_url = \Config::get('mangapie.app_url');
        if ($page < $page_count)
            $next_url = true;
        
        if ($page > 1)
            $prev_url = true;

        return view('manga.reader', compact('id',
                                            'archive_name',
                                            'custom_navbar',
                                            'page',
                                            'page_count',
                                            'next_url',
                                            'prev_url'));
    }

    public function image($id, $archive_name, $page) {
        $manga = Manga::find($id);
        $img = $manga->getImage($archive_name, $page);

        if ($img !== false) {
            // $headers = [
            //     'Content-Type' => $img_mime,
            //     'Content-Length' => ???
            // ];

            return \Response::make($img['data'], 200, [
                'Content-Type' => $img['mime'],
                'Content-Length' => $img['size']
            ]);
        }
        else
            return \Response::make(null, 400);
    }
}
