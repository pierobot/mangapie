<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Manga;
use \App\ImageArchive;
use \App\Thumbnail;

/*
 * TO-DO:
 *  1) Implement logic for determining if a cached image exists for Redis.
 *     Only local FileStore is supported at the moment.
 */

class ThumbnailController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function small($manga_id, $archive_name, $page)
    {
        return \Cache::rememberForever(request()->fullUrl(), function () use ($manga_id, $archive_name, $page) {

                   $manga = Manga::find($manga_id);
                   if ($manga == null) {

                       return \Image::make('public/img/small/unknown.jpg')->response();
                   }

                   $manga_image = $manga->getImage($archive_name, $page);
                   if ($manga_image === false) {

                       return \Image::make('public/img/small/unknown.jpg')->response();
                   }

                   $thumbnail = Thumbnail::make($manga_image['contents'], null, 250);
                   return $thumbnail->response();
               });
    }

    public function smallDefault($manga_id)
    {
        return $this->small($manga_id, null, 1);
    }

    public function medium($manga_id, $archive_name, $page)
    {
        return \Cache::rememberForever(request()->fullUrl(), function () use ($manga_id, $archive_name, $page) {

                   $manga = Manga::find($manga_id);
                   if ($manga == null) {

                       return \Image::make('public/img/medium/unknown.jpg')->response();
                   }

                   $manga_image = $manga->getImage($archive_name, $page);
                   if ($manga_image === false) {

                       return \Image::make('public/img/medium/unknown.jpg')->response();
                   }

                   $thumbnail = Thumbnail::make($manga_image['contents'], null, 500);
                   return $thumbnail->response();
               });
    }

    public function mediumDefault($manga_id)
    {
        return $this->medium($manga_id, null, 1);
    }

    public function update(Request $request)
    {
        dd($request->all());
        return \Redirect::action('MangaInformationController@index')->withInput();
    }
}
