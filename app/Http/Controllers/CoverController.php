<?php

namespace App\Http\Controllers;

use App\Http\Requests\Edit\Cover\CoverUpdateRequest;

use App\Archive;
use App\Library;
use App\Manga;
use App\Cover;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;

class CoverController extends Controller
{
    public function small(Manga $manga, Archive $archive, int $page)
    {
        if (Cover::exists($manga, $archive, $page))
            return Cover::response($manga, $archive, $page);

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            return Cover::defaultResponse();

        $saved = Cover::save($image['contents'], $manga, $archive, $page);

        return $saved === true ? Cover::response($manga, $archive, $page) : Cover::defaultResponse();
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
        if (Cover::exists($manga, $archive, $page, false))
            return Cover::response($manga, $archive, $page, false);

        $image = $manga->getImage($archive, $page);
        if ($image === false)
            return Cover::defaultResponse(false);

        $saved = Cover::save($image['contents'], $manga, $archive, $page, false);

        return $saved === true ? Cover::response($manga, $archive, $page, false) : Cover::defaultResponse(false);
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

    public function put(CoverUpdateRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));
        $archive = Archive::find($request->get('archive_id'));
        $page = $request->get('page');

        $manga->update([
            'cover_archive_id' => $archive->getId(),
            'cover_archive_page' => $page
        ]);

        session()->flash('success', 'The cover was successfully updated');

        return \Redirect::action('MangaController@show', [$manga->getId()]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $deleted = Cover::delete();
        $response = redirect()->back();

        return $deleted ?
            $response->with('success', 'All covers have been deleted.') :
            $response->withErrors('Unable to delete all covers.');
    }
}
