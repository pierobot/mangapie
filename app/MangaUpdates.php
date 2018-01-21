<?php

namespace App;

use \Carbon\Carbon;

use App\IntlString;
use App\Interfaces\AutoFillInterface;

class MangaUpdates implements AutoFillInterface
{
    public static function genres_all()
    {
        $contents = \Curl::to('https://www.mangaupdates.com/genres.html')->get();

        return MangaUpdates::genres_all_ex($contents);
    }

    public static function genres_all_ex($contents)
    {
        $genres_info = [];
        $genres_info_result = preg_match_all('/(<td class=(\"|\')releasestitle(\"|\').+?<b>(.+?)<\/b><\/td>)\s.+\s.+?\s.+?(<td class=(\"|\')text(\"|\') align=(\"|\').+?(\"|\')>(.+?)<br>)/', $contents, $genres_info);
        if ($genres_info_result == 0 || $genres_info_result === false)
            return null;

        $genres = [];
        foreach ($genres_info[4] as $index => $genre_name) {
            array_push($genres, [
                'name' => $genre_name,
                'description' => IntlString::convert(\Html::decode($genres_info[10][$index]))
            ]);
        }

        return $genres;
    }

    public static function search($title, $page, $perpage = 25)
    {
        $contents = \Curl::to('https://www.mangaupdates.com/series.html')->withData([
            'stype' => 'title',
            'search' => urlencode($title),
            'page' => strval($page),
            'perpage' => strval($perpage)
        ])->get();

        return MangaUpdates::search_ex($title, $contents);
    }

    public static function search_ex($title, $contents)
    {
        $results = [];

        $a_elements = [];
        $a_element_count = preg_match_all('/<a href=(\"|\')https?:\/\/(www\.?)?mangaupdates\.com\/series\.html\?id=\d+(\"|\').+alt=(\"|\')Series Info(\"|\')>.+<\/a>/', $contents, $a_elements);

        if ($a_element_count == 0 || $a_element_count === false)
            return false;

        // index 0 contains the <a></a> element
        foreach ($a_elements[0] as $a_element) {
            $urls = [];
            $url_match_count = preg_match_all('/https?:\/\/(www)?\.mangaupdates\.com\/series.html\?id=\d+/', $a_element, $urls);
            if ($url_match_count == 0 || $url_match_count === false)
                continue;

            $names = [];
            $name_match_count = preg_match_all('/alt=(\"|\')Series Info(\"|\')>(<i>)?(.+?)(<\/i>)?<\/a>/', $a_element, $names);
            if ($name_match_count == 0 || $name_match_count === false)
                continue;

            // url decode the names
            array_walk($names[4], function (&$name, $key) {
                $name = IntlString::convert(\Html::decode($name));
            });

            $ids = [];
            $id_match_count = preg_match_all('/\?id=(\d+)/', $urls[0][0], $ids);
            if ($id_match_count == 0 || $id_match_count === false)
                continue;

            array_push($results, [
                'distance' => JaroWinkler::distance($title, $names[4][0]),
                'mu_id' => intval($ids[1][0]),
                'name' => $names[4][0],
                'url' => $urls[0][0],
            ]);
        }

        // sort based on jaro-winkler distance
        usort($results, function ($left, $right) {
            if ($left['distance'] == $right['distance'])
                return 0;
            elseif ($left['distance'] < $right['distance'])
                return 1;
            elseif ($left['distance'] > $right['distance'])
                return -1;
        });

        return $results;
    }

    public static function information($mu_id)
    {
        $contents = \Curl::to('https://www.mangaupdates.com/series.html')->withData([
            'id' => $mu_id
        ])->get();

        return MangaUpdates::information_ex($mu_id, $contents);
    }

    public static function description($contents)
    {
        $description = [];
        $description_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Description<\/b><\/div>\s<div class=(\"|\')sContent(\"|\').+?\">(.+?)\s<\/div>/', $contents, $description);
        if ($description_result != 0) {
            // url decode the description
            array_walk($description[5], function (&$desc, $key) {
                $desc = IntlString::convert(\Html::decode($desc));
            });

            return $description[5][0];
        }

        return null;
    }

    public static function type($contents)
    {
        $type = [];
        $type_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Type<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s+>(.+?)\s<\/div>/', $contents, $type);

        return $type_result != 0 ? $type[5][0] : null;
    }

    public static function associated_names($contents)
    {
        $assoc_names_content = [];
        $assoc_names_content_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Associated Names<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s>(.+?)\s<\/div>/', $contents, $assoc_names_content);
        if ($assoc_names_content_result == 0)
            return null;

        $assoc_names = [];
        $assoc_names_result = preg_match_all('/(.+?)(<br\s\/>)/', $assoc_names_content[5][0], $assoc_names);
        if ($assoc_names_result != 0) {
            // the names are url encoded. take care of that.
            array_walk($assoc_names[1], function (&$name, $key) {

                $name = IntlString::convert(\Html::decode($name));
            }, null);

            return $assoc_names[1];
        }

        return null;
    }

    public static function genres($contents)
    {
        $genres = [];
        $genres_result = preg_match_all('/<a rel=(\"|\').+?genre=.+?<u>(.+?)<\/u><\/a>/', $contents, $genres);

        return $genres_result != 0 ? $genres[2] : null;
    }

    public static function authors($contents)
    {
        $authors_content = [];
        $authors_content_result = preg_match_all('/Author\(s\)<\/b><\/div>\s.+?sContent(\"|\')\s>.+?\s<\/div>/', $contents, $authors_content);
        if ($authors_content_result == 0)
            return null;

        $authors = [];
        $authors_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $authors_content[0][0], $authors);
        if ($authors_result != 0) {
            // url decode the authors' name
            array_walk($authors[2], function (&$author, $key) {
                $author = IntlString::convert(\Html::decode($author));
            });

            return $authors[2];
        } else {
            $authors_result = preg_match_all('/(\"|\')sContent(\"|\')\s?>(.+)&nbsp/', $authors_content[0][0], $authors);
            if ($authors_result != 0) {
                // url decode the authors' name
                array_walk($authors[3], function (&$author, $key) {
                    $author = IntlString::convert(\Html::decode($author));
                });

                return $authors[3];
            }
        }

        return null;
    }

    public static function artists($contents)
    {
        $artists_content = [];
        $artists_content_result = preg_match_all('/(Artist\(s\)<\/b><\/div>\s.+?sContent(\"|\')\s>).+?\s<\/div>/', $contents, $artists_content);
        if ($artists_content_result == 0)
            return null;

        $artists = [];
        $artists_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $artists_content[0][0], $artists);
        if ($artists_result != 0) {
            // url decode the artists' name
            array_walk($artists[2], function (&$artist, $key) {
                $artist = IntlString::convert(\Html::decode($artist));
            });

            return $artists[2];
        } else {
            $artists_result = preg_match_all('/(\"|\')sContent(\"|\')\s?>(.+)&nbsp/', $artists_content[0][0], $artists);
            if ($artists_result != 0) {
                array_walk($artists[3], function (&$artist, $key) {
                    $artist = IntlString::convert(\Html::decode($artist));
                });

                return $artists[3];
            }
        }

        return null;
    }

    public static function year($contents)
    {
        $year = [];
        $year_result = preg_match_all('/(\"|\')sCat(\"|\')><b>Year<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s>(\d+)\s<\/div>/', $contents, $year);

        return $year_result != 0 ? $year[5][0] : null;
    }

    public static function information_ex($mu_id, $contents)
    {
        $information['mu_id'] = $mu_id;
        $information['description'] = MangaUpdates::description($contents);
        $information['type'] = MangaUpdates::type($contents);
        $information['assoc_names'] = MangaUpdates::associated_names($contents);
        $information['genres'] = MangaUpdates::genres($contents);
        $information['authors'] = MangaUpdates::authors($contents);
        $information['artists'] = MangaUpdates::artists($contents);
        $information['year'] = MangaUpdates::year($contents);

        return $information;
    }

    /**
     * Automatically scrapes information about a manga from mangaupdates.
     * This function also saves, and overwrites, the information to the database.
     *
     * @param App\Manga $manga The manga to autofill.
     * @return boolean TRUE on success and FALSE on failure.
     */
    public static function autofill($manga)
    {
        if ($manga == null)
            return false;

        $searchResults = [];
        $exactMatch = false;
        $bestMatchingId = 0;

        // search through five pages for names that will match
        for ($currentPage = 1; $currentPage <= 5; $currentPage++) {
            $pageResults = MangaUpdates::search($manga->getName(), $currentPage);

            if ($pageResults == false || empty($pageResults) == true)
                break;

            // if we have an exact match, then avoid searching other pages
            if ($pageResults[0]['distance'] == 1.0) {
                $exactMatch = true;
                $bestMatchingId = $pageResults[0]['mu_id'];
                break;
            }

            // a perfect match wasn't found, just append
            foreach ($pageResults as $result) {
                array_push($searchResults, $result);
            }
        }

        // if no exact match was found, then sort them in descending order
        if ($exactMatch == false) {
            usort($searchResults, function ($left, $right) {
                if ($left['distance'] == $right['distance'])
                    return 0;
                elseif ($left['distance'] < $right['distance'])
                    return 1;
                elseif ($left['distance'] > $right['distance'])
                    return -1;
            });

            $bestMatchingId = $searchResults[0]['mu_id'];
        }

        // autofill from the id of the name that best matched
        return MangaUpdates::autofillFromId($manga, $bestMatchingId);
    }

    public static function autofillFromId($manga, $id)
    {
        $genre_count = Genre::count();

        // update genres if there are none or if they are older than 6 months
        if ($genre_count == 0) {
            $genres = MangaUpdates::genres_all();
            Genre::populate($genres);
        } else {
            $oldest = Genre::oldest();
            if ($oldest != null && Carbon::now()->subMonths(6)->gt($oldest['updated_at'])) {
                $genres = MangaUpdates::genres_all();
                Genre::populate($genres);
            }
        }

        $information = MangaUpdates::information($id);

        $manga->setMangaUpdatesId($information['mu_id']);
        $manga->setType($information['type']);
        $manga->setDescription($information['description']);
        $assocNamesResult = $manga->addAssociatedNames($information['assoc_names']);
        $authorsResult = $manga->addAuthors($information['authors']);
        $artistsResult = $manga->addArtists($information['artists']);
        $genresResult = $manga->addGenres($information['genres']);
        $manga->setYear($information['year']);

        $manga->save();

        return true;
    }
}
