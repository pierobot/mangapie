<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Symfony\Component\Finder\Finder;

use \App\Manga;
use \App\Library;
use \App\ImageArchive;

class ReaderController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    //
    public function index($id, $archive_name, $page) {
        $manga = Manga::find($id);
        // This controller/view implements a custom navbar
        $custom_navbar = true;

        $archive_path = $manga->getPath() . '/' . $archive_name;
        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return \Response::make(null, 400);

        $images = $archive->getImages();
        if ($images === false)
            return \Response::make(null, 400);

        $page_count = count($images);

        // determine if the navbar will have the previous/next button(s) available
        $prev_url = false;
        $next_url = false;
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
        $image = $manga->getImage($archive_name, $page);

        if ($image !== false) {

            return \Response::make($image['contents'], 200, [
                'Content-Type' => $image['mime'],
                'Content-Length' => $image['size']
            ]);
        }
        else
            return \Response::make(null, 400);
    }
}
