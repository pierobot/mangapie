<?php

namespace App\Http\Controllers;

use App\Archive;
use App\Manga;
use App\Preview;

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

    /**
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function small(Manga $manga, Archive $archive, int $page)
    {
        $cover = new Preview($manga, $archive, $page);
        if (! $cover->exists()) {
            $image = $manga->getImage($archive, $page);
            if ($image) {
                $cover->put($image['contents']);
            }
        }

        return $cover->response();
    }

    /**
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function medium(Manga $manga, Archive $archive, int $page)
    {
        $cover = new Preview($manga, $archive, $page, false);
        if (! $cover->exists()) {
            $image = $manga->getImage($archive, $page);
            if ($image) {
                $cover->put($image['contents']);
            }
        }

        return $cover->response();
    }

    public function destroy()
    {
        $deleted = Preview::delete();
        $response = redirect()->back();

        return $deleted ?
            $response->with('success', 'All previews have been deleted.') :
            $response->withErrors('Unable to delete all previews.');
    }
}
