<?php

namespace App\Http\Controllers;

use App\Archive;
use App\Manga;
use App\Preview;

use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function index(Manga $manga, Archive $archive)
    {
        $pageCount = $archive->getPageCount();
        return view('preview.index')
            ->with('manga', $manga)
            ->with('archive', $archive)
            ->with('pageCount', $pageCount);
    }

    public function small(Manga $manga, Archive $archive, int $page)
    {
        if (Preview::exists($manga, $archive, $page))
            return Preview::response($manga, $archive, $page);

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            return response()->make('', 404);

        Preview::save($image['contents'], $manga, $archive, $page);

        return Preview::response($manga, $archive, $page);
    }

    public function medium(Manga $manga, Archive $archive, int $page)
    {
        if (Preview::exists($manga, $archive, $page, false))
            return Preview::response($manga, $archive, $page, false);

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            return response()->make('', 404);

        Preview::save($image['contents'], $manga, $archive, $page, false);

        return Preview::response($manga, $archive, $page, false);
    }
}
