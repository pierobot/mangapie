<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Archive;
use App\Manga;
use App\Image;
use App\ImageArchive;
use App\ReaderHistory;

class ReaderController extends Controller
{
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

    private function getPreloadUrls(Archive $archive, $page_count, $current_page, $count = 4)
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
            $urls[] = url()->action('ReaderController@image', [$archive->manga->getId(), $archive->getId(), $i]);
        }

        return $urls;
    }

    //
    public function index(Manga $manga, Archive $archive, $page)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        // This controller/view implements a custom navbar
        $custom_navbar = true;

        $path = $manga->getPath();
        $archiveName = $archive->getName();
        $archivePath = $path . '/' . $archiveName;
        $imgArchive = ImageArchive::open($archivePath);
        if ($imgArchive === false)
            return response()->make(null, 400);

        $images = $imgArchive->getImages();
        if ($images === false)
            return response()->make(null, 400);
        // get the image count for this archive
        $page_count = count($images);

        if ($page > $page_count)
            return response()->make(null, 400);

        ReaderHistory::updateOrCreate(
            [
                'user_id' => \Auth::user()->getId(),
                'manga_id' => $id,
                'archive_name' => $archiveName,
                'page_count' => $page_count
            ],
            [
                'user_id' => \Auth::user()->getId(),
                'manga_id' => $id,
                'archive_name' => $archiveName,
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
                $next_archive = $manga->getAdjacentArchive($archiveName);
                // if there's a valid archive then have the next url point to it
                if ($next_archive !== false) {
                    $has_next_page = true;
                    $next_url = url()->action('ReaderController@index', [$id, $next_archive->getId(), 1]);
                }
            } else {
                // just get the url to the next page in this archive
                $has_next_page = true;
                $next_url = url()->action('ReaderController@index', [$id, $archive->getId(), $page + 1]);
            }
        }

        if ($page >= 1) {
            if ($page == 1) {
                // if we're at the first page then get the previous archive
                $prev_archive = $manga->getAdjacentArchive($archiveName, false);
                // if there's a valid archive then have the prev url point to it
                if ($prev_archive !== false) {
                    $prev_archive_path = $path . '/' . $prev_archive['name'];
                    $prev_page_count = $this->getPageCount($prev_archive_path);
                    $has_prev_page = true;
                    $prev_url = url()->action('ReaderController@index', [
                        $id,
                        $prev_archive->getId(),
                        $prev_page_count
                    ]);
                }
            } else {
                // just get the url to the prev page in this archive
                $has_prev_page = true;
                $prev_url = url()->action('ReaderController@index', [$id, $archive->getId(), $page - 1]);
            }
        }

        $preload = $this->getPreloadUrls($archive, $page_count, $page);

        return view('manga.reader')
            ->with('id', $id)
            ->with('manga', $manga)
            ->with('name', $name)
            ->with('archive', $archive)
            ->with('archive_name', $archiveName)
            ->with('custom_navbar', $custom_navbar)
            ->with('page', $page)
            ->with('page_count', $page_count)
            ->with('preload', $preload)
            ->with('has_next_page', $has_next_page)
            ->with('has_prev_page', $has_prev_page)
            ->with('next_url', $next_url)
            ->with('prev_url', $prev_url)
            ->with('ltr', \Auth::user()->getLtr());
    }

    public function image(Manga $manga, Archive $archive, $page)
    {
        return Image::response($manga, $archive, $page);
    }
}
