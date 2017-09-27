<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Manga;
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

    private function smallKey($id, $archive_name, $page)
    {
        return 'small-' . strval($id) . '-' . $archive_name . '-' . $page;
    }

    private function mediumKey($id, $archive_name, $page)
    {
        return 'medium-' . strval($id) . '-' . $archive_name . '-' . $page;
    }

    private function getSmallResponse($id, $archive_name, $page)
    {
        $manga = Manga::find($id);
        if ($manga == null) {
            return \Image::make('public/img/small/unknown.jpg')->response();
        }

        $manga_image = $manga->getImage($archive_name, $page);
        if ($manga_image === false) {
            return \Image::make('public/img/small/unknown.jpg')->response();
        }

        $thumbnail = Thumbnail::make($manga_image['contents'], null, 250);
        return $thumbnail->response();
    }

    public function small($id, $archive_name, $page)
    {
        $key = $this->smallKey($id, $archive_name, $page);
        if (\Cache::has($key)) {
            $smallResponse = \Cache::get($key);
        } else {
            $smallResponse = $this->getSmallResponse($id, $archive_name, $page);
            \Cache::forever($key, $smallResponse);
        }

        return $smallResponse;
    }

    public function smallDefault($id)
    {
        return $this->small($id, null, 1);
    }

    private function getMediumResponse($id, $archive_name, $page)
    {
        $manga = Manga::find($id);
        if ($manga == null) {

            return \Image::make('public/img/medium/unknown.jpg')->response();
        }

        $manga_image = $manga->getImage($archive_name, $page);
        if ($manga_image === false) {

            return \Image::make('public/img/medium/unknown.jpg')->response();
        }

        $thumbnail = Thumbnail::make($manga_image['contents'], null, 500);
        return $thumbnail->response();
    }

    public function medium($id, $archive_name, $page)
    {
        $key = $this->mediumKey($id, $archive_name, $page);
        if (\Cache::has($key)) {
            $mediumResponse = \Cache::get($key);
        } else {
            $mediumResponse = $this->getMediumResponse($id, $archive_name, $page);
            \Cache::forever($key, $mediumResponse);
        }

        return $mediumResponse;
    }

    public function mediumDefault($id)
    {
        return $this->medium($id, null, 1);
    }

    public function update(Request $request)
    {
        if (\Auth::user()->isAdmin() == false && \Auth::user()->isMaintainer() == false) {
            return view('errors.403');
        }

        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer',
            'archive_name' => 'required|string',
            'page' => 'required|integer',
        ]);

        $id = \Input::get('id');
        if ($validator->fails()) {

            return \Redirect::action('MangaInformationController@index', [$id])
                            ->withErrors($validator, 'update');
        }

        $archive_name = \Input::get('archive_name');
        $page = \Input::get('page');
        // archive name and page are always null for the default thumbnail
        $small_key = $this->smallKey($id, null, 1);
        $medium_key = $this->mediumKey($id, null, 1);
        // forget the cached thumbnails if they exist
        if (\Cache::has($small_key)) {

            \Cache::forget($small_key);
            \Cache::forever($small_key, $this->getSmallResponse($id, $archive_name, $page));
        }

        if (\Cache::has($medium_key)) {

            \Cache::forget($medium_key);
            \Cache::forever($medium_key, $this->getMediumResponse($id, $archive_name, $page));
        }

        \Session::flash('thumbnail-update-success', 'The thumbnail was successfully updated');

        return \Redirect::action('MangaInformationController@index', [$id]);
    }
}
