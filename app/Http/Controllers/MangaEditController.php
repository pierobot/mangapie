<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditMangaArtistAddRequest;
use App\Http\Requests\EditMangaArtistRemoveRequest;
use App\Http\Requests\EditMangaAssocNameAddRequest;
use App\Http\Requests\EditMangaAssocNameRemoveRequest;
use App\Http\Requests\EditMangaAuthorAddRequest;
use App\Http\Requests\EditMangaAuthorRemoveRequest;
use App\Http\Requests\EditMangaAutofillRequest;
use App\Http\Requests\EditMangaDescriptionRequest;
use App\Http\Requests\EditMangaTypeRequest;
use App\Http\Requests\EditMangaYearRequest;

use App\Artist;
use App\AssociatedName;
use App\Author;
use App\Manga;
use App\Genre;
use App\Http\Requests\EditMangaGenresRequest;
use App\MangaUpdates;

class MangaEditController extends Controller
{
    public function index(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $mu_id = $manga->getMangaUpdatesId();

        return view('edit.manga.index')
            ->with('id', $id)
            ->with('mu_id', $mu_id)
            ->with('name', $name);
    }

    public function mangaupdates(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();

        return view('edit.manga.mangaupdates')
            ->with('id', $id)
            ->with('name', $name);

    }

    public function description(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $description = $manga->getDescription();

        return view('edit.manga.information.description')
            ->with('id', $id)
            ->with('name', $name)
            ->with('description', $description);
    }

    public function type(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $type = $manga->getType();

        return view('edit.manga.information.type')
            ->with('id', $id)
            ->with('name', $name)
            ->with('type', $type);
    }

    public function names(Manga $manga)
    {
        $manga = $manga->load('associatedNameReferences.associatedName');

        $id = $manga->getId();
        $name = $manga->getName();

        return view('edit.manga.information.names')
            ->with('id', $id)
            ->with('name', $name)
            ->with('assocNameReferences', $manga->associatedNameReferences);
    }

    public function genres(Manga $manga)
    {
        $manga = $manga->load('genreReferences.genre');

        return view('edit.manga.information.genres')
            ->with('id', $manga->id)
            ->with('name', $manga->name)
            ->with('manga', $manga);
    }

    public function authors(Manga $manga)
    {
        $manga = $manga->load('authorReferences.author');

        $id = $manga->getId();
        $name = $manga->getName();

        return view('edit.manga.information.authors')
            ->with('id', $id)
            ->with('name', $name)
            ->with('authorReferences', $manga->authorReferences);
    }

    public function artists(Manga $manga)
    {
        $manga = $manga->load('artistReferences.artist');

        $id = $manga->getId();
        $name = $manga->getName();

        return view('edit.manga.information.artists')
            ->with('id', $id)
            ->with('name', $name)
            ->with('artistReferences', $manga->artistReferences);
    }

    public function year(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $year = $manga->getYear();

        return view('edit.manga.information.year')
            ->with('id', $id)
            ->with('name', $name)
            ->with('year', $year);
    }

    public function covers(Manga $manga)
    {
        $id = $manga->getId();
        $name = $manga->getName();
        $archives = $manga->getArchives();

        return view('edit.manga.covers')
            ->with('id', $id)
            ->with('name', $name)
            ->with('archives', $archives);
    }

    public function patchAutofill(EditMangaAutofillRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = MangaUpdates::autofillFromId($manga, \Input::get('mu_id'));

        $response = redirect()->action('MangaEditController@mangaupdates', [$manga->id]);

        if (! $successful)
            return $response->withErrors(['autofill' => 'Unable to scrape mangaupdates for information.']);

        session()->flash('success', 'The information was successfully autofilled.');

        return $response;
    }

    public function updateGenres(EditMangaGenresRequest $request)
    {
        $allGenres = Genre::all();
        $deletedSuccessfully = false;

        $manga = Manga::find($request->get('manga_id'))->load('genreReferences.genre');
        $response = redirect()->action('MangaEditController@genres', [$manga->id]);

        $newGenres = collect($request->get('genres'));

        // all the genre ids not in the manga's genre references are those that should be removed
        $toDelete = $manga->genreReferences()->whereNotIn('genre_id', $newGenres);
        if ($toDelete->count() > 0) {
            $deletedSuccessfully = $toDelete->forceDelete();

            if (! $deletedSuccessfully)
                return $response->withErrors('Unable to remove all the specified genres.');
        }

        // filter out the genre ids that do not exist in the manga's genre references
        $newGenres = $newGenres->filter(function ($genreId) use ($manga) {
            return ! $manga->genreReferences->firstWhere('genre_id', $genreId);
        });

        $addedCount = 0;
        // create a reference for each genre that needs to be added
        $newGenres->each(function ($genreId) use ($manga, $allGenres, &$addedCount) {
            $addedSuccessfully = ! empty($manga->genreReferences()->create([
                'genre_id' => $allGenres->find($genreId)->id
            ]));

            if ($addedSuccessfully)
                ++$addedCount;
        });

        $addedSuccessfully = $addedCount === $newGenres->count();
        if (! $addedSuccessfully)
            return $response->withErrors('Unable to add all the specified genres.');

        session()->flash('success', 'The genres were successfully updated.');

        return $response;
    }

    public function patchDescription(EditMangaDescriptionRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'description' => $request->get('description')
        ]);

        $response = redirect()->action('MangaEditController@description', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to update the description.');

        session()->flash('success', 'The description was successfully updated.');

        return $response;
    }

    public function deleteDescription(EditMangaDescriptionRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'description' => null
        ]);

        $response = redirect()->action('MangaEditController@description', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to update the description.');

        session()->flash('success', 'The description was successfully updated.');

        return $response;
    }

    public function patchAssocName(EditMangaAssocNameAddRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $name = $manga->associatedNameReferences()->firstOrCreate([
            'assoc_name_id' => AssociatedName::firstOrCreate([
                'name' => $request->get('name'),
            ])->id
        ]);

        $response = redirect()->action('MangaEditController@names', [$manga->id]);

        if (empty($name))
            return $response->withErrors('Unable to add associated name.');

        session()->flash('success', 'The associated name was successfully added.');

        return $response;
    }

    public function deleteAssocName(EditMangaAssocNameRemoveRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->associatedNameReferences()
            ->find($request->get('associated_name_reference_id'))
            ->forceDelete();

        $response = redirect()->action('MangaEditController@names', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to delete associated name.');

        session()->flash('success', 'The associated name was successfully deleted.');

        return $response;
    }

    public function patchAuthor(EditMangaAuthorAddRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->authorReferences()
            ->firstOrCreate([
                'author_id' => Author::firstOrCreate([
                    'name' => $request->get('name')
                ])->id
            ]);

        $response = redirect()->action('MangaEditController@authors', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to create author.');

        session()->flash('success', 'The author was successfully created.');

        return $response;
    }

    public function deleteAuthor(EditMangaAuthorRemoveRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->authorReferences()
            ->find($request->get('author_reference_id'))
            ->forceDelete();

        $response = redirect()->action('MangaEditController@authors', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to remove author.');

        session()->flash('success', 'The author was successfully removed.');

        return $response;
    }

    public function patchArtist(EditMangaArtistAddRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->artistReferences()
            ->firstOrCreate([
                'artist_id' => Artist::firstOrCreate([
                    'name' => $request->get('name')
                ])->id
            ]);

        $response = redirect()->action('MangaEditController@artists', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to create artist.');

        session()->flash('success', 'The artist was successfully created.');

        return $response;
    }

    public function deleteArtist(EditMangaArtistRemoveRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->artistReferences()
            ->find($request->get('artist_reference_id'))
            ->forceDelete();

        $response = redirect()->action('MangaEditController@artists', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to remove artist.');

        session()->flash('success', 'The artist was successfully removed.');

        return $response;
    }

    public function patchType(EditMangaTypeRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'type' => $request->get('type')
        ]);

        $response = redirect()->action('MangaEditController@type', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to update type.');

        session()->flash('success', 'The type was successfully updated.');

        return $response;
    }

    public function deleteType(EditMangaTypeRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'type' => null
        ]);

        $response = redirect()->action('MangaEditController@type', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to delete type.');

        session()->flash('success', 'The type was successfully deleted.');

        return $response;
    }

    public function patchYear(EditMangaYearRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'year' => $request->get('year')
        ]);

        $response = redirect()->action('MangaEditController@year', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to update year.');

        session()->flash('success', 'The year was successfully updated.');

        return $response;
    }

    public function deleteYear(EditMangaYearRequest $request)
    {
        $manga = Manga::find($request->get('manga_id'));

        $successful = $manga->update([
            'year' => null
        ]);

        $response = redirect()->action('MangaEditController@year', [$manga->id]);

        if (! $successful)
            return $response->withErrors('Unable to delete year.');

        session()->flash('success', 'The year was successfully deleted.');

        return $response;
    }
}
