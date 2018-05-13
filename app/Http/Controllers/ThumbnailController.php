<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;

use \App\Manga;
use \App\Thumbnail;

class ThumbnailController extends Controller
{
    private function smallKey($id, $archive_name, $page)
    {
        return 'small-' . strval($id) . '-' . $archive_name . '-' . $page;
    }

    private function mediumKey($id, $archive_name, $page)
    {
        return 'medium-' . strval($id) . '-' . $archive_name . '-' . $page;
    }

    private function getSmallResponse(Manga $manga, $archive_name, $page)
    {
        $manga_image = $manga->getImage($archive_name, $page);
        if ($manga_image === false) {
            return \Image::make('public/img/small/unknown.jpg')->response();
        }

        try
        {
            $thumbnail = Thumbnail::make($manga_image['contents'], null, 250);

            return $thumbnail->response();
        }
        catch (\Intervention\Image\Exception\NotReadableException $e)
        {
            return \Image::make('public/img/small/unknown.jpg')->response();
        }
    }

    public function small(Manga $manga, $archive_name, $page)
    {
        $id = $manga->getId();
        $key = $this->smallKey($id, $archive_name, $page);

        if (\Cache::has($key)) {
            $smallResponse = \Cache::get($key);
        } else {
            $smallResponse = $this->getSmallResponse($manga, $archive_name, $page);
            \Cache::forever($key, $smallResponse);
        }

        return $smallResponse->withHeaders([
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String()
        ]);
    }

    public function smallDefault(Manga $manga)
    {
        return $this->small($manga, null, 1);
    }

    private function getMediumResponse(Manga $manga, $archive_name, $page)
    {
        $manga_image = $manga->getImage($archive_name, $page);
        if ($manga_image === false) {

            return \Image::make('public/img/medium/unknown.jpg')->response();
        }

        try
        {
            $thumbnail = Thumbnail::make($manga_image['contents'], null, 500);

            return $thumbnail->response();
        }
        catch (\Intervention\Image\Exception\NotReadableException $e)
        {
            return \Image::make('public/img/medium/unknown.jpg')->response();
        }
    }

    public function medium(Manga $manga, $archive_name, $page)
    {
        $id = $manga->getId();
        $key = $this->mediumKey($id, $archive_name, $page);
        if (\Cache::has($key)) {
            $mediumResponse = \Cache::get($key);
        } else {
            $mediumResponse = $this->getMediumResponse($manga, $archive_name, $page);
            \Cache::forever($key, $mediumResponse);
        }

        return $mediumResponse->withHeaders([
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String()
        ]);
    }

    public function mediumDefault(Manga $manga)
    {
        return $this->medium($manga, null, 1);
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

            return \Redirect::action('MangaController@index', [$id])
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

        return \Redirect::action('MangaController@index', [$id]);
    }
}
