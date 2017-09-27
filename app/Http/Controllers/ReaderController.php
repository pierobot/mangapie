<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Manga;
use \App\ImageArchive;

class ReaderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getPageCount($archive_path)
    {
        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return false;

        $images = $archive->getImages();
        if ($images === false)
            return false;

        return count($images);
    }

    //
    public function index($id, $archive_name, $page)
    {
        $manga = Manga::find($id);
        $name = $manga->getName();
        // This controller/view implements a custom navbar
        $custom_navbar = true;

        $path = $manga->getPath();
        $archive_path = $path . '/' . $archive_name;
        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return \Response::make(null, 400);

        $images = $archive->getImages();
        if ($images === false)
            return \Response::make(null, 400);
        // get the image count for this archive
        $page_count = count($images);

        $has_prev_page = false;
        $prev_archive = false;
        $has_next_page = false;
        $next_archive = false;

        $prev_url = false;
        $next_url = false;

        if ($page <= $page_count) {
            if ($page == $page_count) {
                // if we're at the last page then get the next archive
                $next_archive = $manga->getAdjacentArchive($archive_name);
                // if there's a valid archive then have the next url point to it
                if ($next_archive !== false) {
                    $has_next_page = true;
                    $next_url = \URL::action('ReaderController@index', [$id, rawurlencode($next_archive['name']), 1]);
                }
            } else {
                // just get the url to the next page in this archive
                $has_next_page = true;
                $next_url = \URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page + 1]);
            }
        }

        if ($page >= 1) {
            if ($page == 1) {
                // if we're at the first page then get the previous archive
                $prev_archive = $manga->getAdjacentArchive($archive_name, false);
                // if there's a valid archive then have the prev url point to it
                if ($prev_archive !== false) {
                    $prev_archive_path = $path . '/' . $prev_archive['name'];
                    $prev_page_count = $this->getPageCount($prev_archive_path);
                    $has_prev_page = true;
                    $prev_url = \URL::action('ReaderController@index', [
                        $id,
                        rawurlencode($prev_archive['name']),
                        $prev_page_count]);
                }
            } else {
                // just get the url to the prev page in this archive
                $has_prev_page = true;
                $prev_url = \URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page - 1]);
            }
        }

        return view('manga.reader', compact('id',
                                            'name',
                                            'archive_name',
                                            'custom_navbar',
                                            'page',
                                            'page_count',
                                            'has_next_page',
                                            'next_url',
                                            'prev_url',
                                            'has_prev_page'));
    }

    public function image($id, $archive_name, $page)
    {
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
