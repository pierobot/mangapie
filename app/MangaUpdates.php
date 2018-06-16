<?php

namespace App;

use \Carbon\Carbon;

use App\IntlString;
use App\Interfaces\AutoFillInterface;

class MangaUpdates implements AutoFillInterface
{
    public static function encodeNCR($str)
    {
        $result = '';

        $length = IntlString::strlen($str);
        for ($i = 0, $offset = 0, $next = 0; $i < $length; $i++) {
            $grapheme = IntlString::grapheme($str, $offset, $next);
            $codepoint = mb_ord($grapheme, 'UTF-8');

            // If the code point is not ASCII then we have to append &#codepoint;
            if ($codepoint > 255)
                $result .= '&#' . $codepoint . ';';
            else
                $result .= $grapheme;

            $offset = $next;
        }

        return $result;
    }

    public static function search($title, $page, $perpage = 50)
    {
        $contents = \Curl::to('https://www.mangaupdates.com/series.html')->withData([
            'stype' => 'title',
            'search' => self::encodeNCR($title),
            'page' => strval($page),
            'perpage' => strval($perpage),
            'output' => 'json'
        ])->post();

        return MangaUpdates::search_ex($title, $contents);
    }

    public static function search_ex($title, $contents)
    {
        $results = [];

        $json = json_decode($contents);
        if (empty($json) ||
            empty($json->{'results'}) ||
            empty($json->{'results'}->{'items'})) {

            return $results;
        }

        $items = $json->{'results'}->{'items'};
        foreach ($items as $item) {
            $mu_id = intval($item->{'id'});
            $url = 'https://www.mangaupdates.com/series.html?id=' . $item->{'id'};
            $item_title = \Html::decode($item->{'title'});
            $distance = JaroWinkler::distance($title, $item_title);

            array_push($results, [
                'mu_id' => $mu_id,
                'url' => $url,
                'name' => $item_title,
                'distance' => $distance
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
        $authors_result = preg_match_all('/\?id=([0-9]+).+?<u>(.+?)<\/u>/', $authors_content[0][0], $authors);
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
        $artists_result = preg_match_all('/\?id=([0-9]+).+?<u>(.+?)<\/u>/', $artists_content[0][0], $artists);
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
        $year_result = preg_match_all('/(\"|\')sCat(\"|\')><b>Year<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s>([0-9]+)\s<\/div>/', $contents, $year);

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
        if ($manga == null || $manga->getIgnoreOnScan() == true)
            return false;

        $searchResults = [];
        $exactMatch = false;

        // search through three pages for names that will match
        for ($currentPage = 1; $currentPage <= 3; $currentPage++) {
            $pageResults = MangaUpdates::search($manga->getName(), $currentPage);

            if ($pageResults == false || empty($pageResults) == true)
                break;

            // if we have an exact match, then avoid searching other pages
            if ($pageResults[0]['distance'] == 1.0) {
                $exactMatch = true;
                $searchResults = [];
                array_push($searchResults, $pageResults[0]);
                break;
            }

            // a perfect match wasn't found, just append
            foreach ($pageResults as $result) {
                array_push($searchResults, $result);
            }

            // wait for a couple of seconds before requesting the next page
            sleep(2);
        }

        if (empty($searchResults) == true) {
            \Log::warning(__METHOD__ . ' failure.', [
                'id' => $manga->getId(),
                'name' => $manga->getName(),
            ]);

            // there was a failure for whatever reason.
            $manga->setIgnoreOnScan(true);
            $manga->save();

            return false;
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
        }

        $manga->setMangaUpdatesName($searchResults[0]['name']);
        $manga->setDistance($searchResults[0]['distance']);
        $manga->save();

        // autofill from the id of the name that best matched
        return MangaUpdates::autofillFromId($manga, $searchResults[0]['mu_id']);
    }

    public static function autofillFromId($manga, $id)
    {
        $information = MangaUpdates::information($id);

        if (empty($information) == true) {
            \Log::warning(__METHOD__ . ' failure.', [
                'id' => $manga->getId(),
                'name' => $manga->getName(),
                'mu_id' => $id
            ]);

            return false;
        }

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
