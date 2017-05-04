<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Get the image count
        $archive_path = $manga->getPath() . '/' . $archive_name;
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
