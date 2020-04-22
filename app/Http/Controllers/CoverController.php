<?php

namespace App\Http\Controllers;

use App\Http\Requests\Edit\Cover\CoverUpdateRequest;

use App\Archive;
use App\Manga;
use App\Cover;

class CoverController extends Controller
{
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
        $cover = new Cover($manga, $archive, $page);
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
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function smallDefault(Manga $manga)
    {
        $archive = Archive::find($manga->cover_archive_id);
        $page = $manga->cover_archive_page;

        if (empty($archive)) {
            $archive = $manga->archives->first();

            $manga->update([
                'cover_archive_id' => $archive->id
            ]);
        }

        return $this->small($manga, $archive, $page);
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
        $cover = new Cover($manga, $archive, $page, false);
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
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function mediumDefault(Manga $manga)
    {
        $archive = Archive::find($manga->cover_archive_id);
        $page = $manga->cover_archive_page;

        if (empty($archive)) {
            $archive = $manga->archives->first();

            $manga->update([
                'cover_archive_id' => $archive->id
            ]);
        }

        return $this->medium($manga, $archive, $page);
    }

    /**
     * @param CoverUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function put(CoverUpdateRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));
        $archive = Archive::find($request->get('archive_id'));
        $page = $request->get('page');

        $manga->update([
            'cover_archive_id' => $archive->id,
            'cover_archive_page' => $page
        ]);

        session()->flash('success', 'The cover was successfully updated');

        return \Redirect::action('MangaController@show', [$manga->id]);
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
