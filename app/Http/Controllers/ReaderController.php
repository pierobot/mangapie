<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateReaderHistory;

use App\Archive;
use App\Manga;
use App\Image;
use App\ImageArchive;

class ReaderController extends Controller
{
    public function index(Manga $manga, Archive $archive, int $page)
    {
        $imgArchive = ImageArchive::open($manga->path . DIRECTORY_SEPARATOR . $archive->name);
        if ($imgArchive === false)
            return response()->make(null, 400);

        $images = $imgArchive->getImages();
        if ($images === false)
            return response()->make(null, 400);

        $pageCount = count($images);

        return view('manga.reader')
            ->with('manga', $manga)
            ->with('archive', $archive)
            ->with('page', $page)
            ->with('pageCount', $pageCount);
    }

    public function image(Manga $manga, Archive $archive, int $page)
    {
        return Image::response($manga, $archive, $page);
    }
}
