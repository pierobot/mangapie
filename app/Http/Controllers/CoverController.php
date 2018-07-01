<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoverUpdateRequest;
use App\Listeners\DirectoryEventSubscriber;
use Illuminate\Http\Request;
use \Carbon\Carbon;

use App\Archive;
use App\Manga;
use App\Cover;

class CoverController extends Controller
{
    public function small(Manga $manga, Archive $archive, int $page)
    {
        $response = response()->make('', 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String(),
            'X-Accel-Redirect' => '/covers/' . Cover::xaccelPath($manga, $archive, $page),
            'X-Accel-Charset' => 'utf-8'
        ]);

        if (Cover::exists($manga, $archive, $page))
            return $response;

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            // TODO: REPLACE WITH X-Accel-Redirect
            return \Image::make('public/img/small/unknown.jpg')->response();

        try {
            Cover::createPath($manga, $archive);

            $path = Cover::storage_path() . DIRECTORY_SEPARATOR . Cover::xaccelPath($manga, $archive, $page);
            $cover = Cover::make($image['contents'], null, 250)->save($path);

            return $response;
        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            // TODO: REPLACE WITH X-Accel-Redirect
            return \Image::make('public/img/small/unknown.jpg')->response();
        }
    }

    public function smallDefault(Manga $manga)
    {
        $archive = $manga->getCoverArchive();
        $page = $manga->getCoverPage();

        if (empty($archive)) {
            $archive = $manga->archives->first();

            $manga->update([
                'cover_archive_id' => $archive->getId()
            ]);
        }

        return $this->small($manga, $archive, $page);
    }

    public function medium(Manga $manga, Archive $archive, int $page)
    {
        $response = response()->make('', 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String(),
            'X-Accel-Redirect' => '/covers/' . Cover::xaccelPath($manga, $archive, $page, false),
            'X-Accel-Charset' => 'utf-8'
        ]);

        if (Cover::exists($manga, $archive, $page, false))
            return $response;

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            // TODO: REPLACE WITH X-Accel-Redirect
            return \Image::make('public/img/medium/unknown.jpg')->response();

        try {
            Cover::createPath($manga, $archive, false);

            $path = Cover::storage_path() . DIRECTORY_SEPARATOR . Cover::xaccelPath($manga, $archive, $page, false);
            $cover = Cover::make($image['contents'], null, 500)->save($path);

            return $response;
        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            // TODO: REPLACE WITH X-Accel-Redirect
            return \Image::make('public/img/medium/unknown.jpg')->response();
        }
    }

    public function mediumDefault(Manga $manga)
    {
        $archive = $manga->getCoverArchive();
        $page = $manga->getCoverPage();

        if (empty($archive)) {
            $archive = $manga->archives->first();

            $manga->update([
                'cover_archive_id' => $archive->getId()
            ]);
        }

        return $this->medium($manga, $archive, $page);
    }

    public function update(CoverUpdateRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));
        $archive = Archive::find($request->get('archive_id'));
        $page = $request->get('page');

        $manga->update([
            'cover_archive_id' => $archive->getId(),
            'cover_archive_page' => $page
        ]);

        session()->flash('success', 'The thumbnail was successfully updated');

        return \Redirect::action('MangaController@index', [$manga->getId()]);
    }
}
