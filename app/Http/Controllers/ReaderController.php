<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use \App\Manga;
use \App\ImageArchive;
use \App\ReaderHistory;

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

    private function getPreloadUrls($id, $archive_name, $page_count, $current_page, $count = 4)
    {
        if ($page_count <= 0 || $count == 0 || $current_page == $page_count)
            return false;

        // ensure we only build up to $count or less. +1 because we don't count the current page
        $difference = $page_count - $current_page;
        if ($difference >= $count) {
            $count = 4;
        } else {
            $count = $difference;
        }

        $urls = [];
        ++$current_page;
        for ($i = $current_page; $i < $current_page + $count; $i++) {
            array_push($urls, [
               'id' => strval($i),
               'url' => \URL::action('ReaderController@image', [$id, rawurlencode($archive_name), $i])
            ]);
        }

        return $urls;
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

        if ($page > $page_count)
            return \Response::make(null, 400);

        ReaderHistory::updateOrCreate(
            [
                'user_id' => \Auth::user()->getId(),
                'manga_id' => $id,
                'archive_name' => $archive_name,
                'page_count' => $page_count
            ],
            [
                'user_id' => \Auth::user()->getId(),
                'manga_id' => $id,
                'archive_name' => $archive_name,
                'page' => $page,
                'page_count' => $page_count
            ]
        );

        $has_prev_page = false;
        $has_next_page = false;

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

        $preload = $this->getPreloadUrls($id, $archive_name, $page_count, $page);

        return view('manga.reader')->with('id', $id)
                                   ->with('name', $name)
                                   ->with('archive_name', $archive_name)
                                   ->with('custom_navbar', $custom_navbar)
                                   ->with('page', $page)
                                   ->with('page_count', $page_count)
                                   ->with('preload', $preload)
                                   ->with('has_next_page', $has_next_page)
                                   ->with('has_prev_page', $has_prev_page)
                                   ->with('next_url', $next_url)
                                   ->with('prev_url', $prev_url);
    }

    public function image($id, $archive_name, $page)
    {
        $manga = Manga::find($id);
        $image = $manga->getImage($archive_name, $page);

        if ($image == false)
            return \Response::make(null, 400);

        return \Response::make($image['contents'], 200, [
            'Content-Type' => $image['mime'],
            'Content-Length' => $image['size'],
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String()
        ]);
    }
}
