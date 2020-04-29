<?php

namespace App\Sources;

use App\AssociatedName;
use App\Genre;
use App\Interfaces\AutoFillInterface;
use App\Person;
use App\Sources\MangaUpdates\Series;

use App\Manga;

class MangaUpdates implements AutoFillInterface
{
    /**
     * Automatically scrapes information about a manga from mangaupdates.
     * This function also saves, and overwrites, the information to the database.
     *
     * @param Manga $manga The manga to autofill.
     * @return bool
     */
    public static function autofill($manga)
    {
        $result = false;

        if ($manga != null || $manga->ignore_on_scan == false) {
            $results = Series::search($manga->name);

            if (! empty($results)) {
                $manga->update([
                    'mu_name' => $results[0]['name'],
                    'distance' => $results[0]['distance']
                ]);
    
                // autofill from the id of the name that best matched
                $result = self::autofillFromId($manga, $results[0]['mu_id']);
            }
        }            

        return $result;
    }

    /**
     * Automatically fills information about a manga from mangaupdates.
     *
     * @param Manga $manga The manga to autofill.
     * @param int $id The mangaupdates id.
     * @return bool
     */
    public static function autofillFromId($manga, $id)
    {
        $information = Series::information($id);

        if (! empty($information)) {
            \DB::transaction(function () use ($manga, $information) {
                /*
                 * Remove all currently present information and relations as we will be
                 * overriding it with what is retrieved from the auto fill.
                 */
                $manga->authors()->forceDelete();
                $manga->artists()->forceDelete();
                $manga->genres()->forceDelete();
                $manga->associatedNames()->forceDelete();

                $manga->update([
                    'mu_id' => $information['mu_id'],
                    'type' => $information['type'],
                    'description' => $information['description'],
                    'year' => $information['year'],
                ]);

                $mangaId = $manga->id;

                $associatedNames = collect($information['assoc_names'])->transform(
                    function (string $name) use ($mangaId) {
                        return [
                            'manga_id' => $mangaId,
                            'associated_name_id' => AssociatedName::firstOrCreate([
                                'name' => $name
                            ])->id
                        ];
                    }
                )->toArray();

                $authors = collect($information['authors'])->transform(
                    function (string $name) use ($mangaId) {
                        return [
                            'manga_id' => $mangaId,
                            'author_id' => Person::firstOrCreate([
                                'name' => $name
                            ])->id
                        ];
                    }
                )->toArray();

                $artists = collect($information['artists'])->transform(
                    function (string $name) use ($mangaId) {
                        return [
                            'manga_id' => $mangaId,
                            'artist_id' => Person::firstOrCreate([
                                'name' => $name
                            ])->id
                        ];
                    }
                )->toArray();

                $genres = collect($information['genres'])->transform(
                    function (string $name) use ($mangaId) {
                        return [
                            'manga_id' => $mangaId,
                            'genre_id' => Genre::firstOrCreate([
                                'name' => $name
                            ])->id
                        ];
                    }
                )->toArray();

                $manga->associatedNameReferences()->createMany($associatedNames);
                $manga->authorReferences()->createMany($authors);
                $manga->artistReferences()->createMany($artists);
                $manga->genreReferences()->createMany($genres);
            });

            return true;
        }

        return false;
    }
}
