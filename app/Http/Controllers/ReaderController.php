<?php

namespace App\Http\Controllers;

use App\Archive;
use App\Http\Requests\Reader\PutReaderHistoryRequest;
use App\Manga;
use App\Image;
use App\ImageArchive;
use App\ReaderHistory;

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

    public function putReaderHistory(PutReaderHistoryRequest $request)
    {
        // TODO: Remove the lines below and the archive_name attribute in favor of archive_id, which will require a migration
        // I have no clue why I used archive_name. No clue. Well I'm not even at v0.1 so w/e atpRtsd
        $manga = Manga::findOrFail($request->get('manga_id'));
        $archive = Archive::findOrFail($request->get('archive_id'));

        $imgArchive = ImageArchive::open($manga->path . DIRECTORY_SEPARATOR . $archive->name);
        if ($imgArchive === false)
            return response()->make(null, 400);

        $images = $imgArchive->getImages();
        if ($images === false)
            return response()->make(null, 400);

        $pageCount = count($images);

        ReaderHistory::updateOrCreate([
            'user_id' => $request->user()->id,
            'manga_id' => $request->get('manga_id'),
            'archive_name' => $archive->name,
            'page_count' => $pageCount,
        ], [
            'user_id' => $request->user()->id,
            'manga_id' => $request->get('manga_id'),
            'archive_name' => $archive->name,
            'page' => $request->get('page'),
            'page_count' => $pageCount,
        ]);
    }
}
