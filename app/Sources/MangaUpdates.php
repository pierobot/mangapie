<?php

namespace App\Sources;

use App\Interfaces\AutoFillInterface;
use App\Sources\MangaUpdates\Series;

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

        if ($manga != null || $manga->getIgnoreOnScan() == false) {
            $results = Series::search($manga->getName());

            if (empty($results) == false) {
                $manga->setMangaUpdatesName($results[0]['name']);
                $manga->setDistance($results[0]['distance']);
                $manga->save();
    
                // autofill from the id of the name that best matched
                $result = self::autofillFromId($manga, $results[0]['mu_id']);
            }
        }            

        return $result;
    }

    /**
     * Automatically fills information about a manga from mangaupdates.
     * @param Manga $manga The manga to autofill.
     * @param int $id The mangaupdates id.
     * @return bool
     */
    public static function autofillFromId($manga, $id)
    {
        $result = false;
        $information = Series::information($id);

        if (! empty($information)) {
            $manga->authorReferences()->forceDelete();
            $manga->artistReferences()->forceDelete();
            $manga->genreReferences()->forceDelete();
            $manga->associatedNameReferences()->forceDelete();

            $manga->setMangaUpdatesId($information['mu_id']);
            $manga->setType($information['type']);
            $manga->setDescription($information['description']);
            $manga->addAssociatedNames($information['assoc_names']);
            $manga->addAuthors($information['authors']);
            $manga->addArtists($information['artists']);
            $manga->addGenres($information['genres']);
            $manga->setYear($information['year']);
    
            $manga->save();

            $result = true;
        }

        return $result;
    }
}
