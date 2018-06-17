<?php

namespace App;

use \Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

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

    public static function description(Crawler $crawler)
    {
        return trim($crawler->filter('div.sMember > div.sCat + div.sContent')
                            ->eq(0)
                            ->text());
    }

    public static function type(Crawler $crawler)
    {
        return trim($crawler->filter('div.sMember > div.sCat + div.sContent')
                            ->eq(1)
                            ->text());
    }

    public static function associated_names(Crawler $crawler)
    {
        $currentElement = $crawler->filter('div.sContainer')
                                  ->eq(0) 
                                  ->filter('div.sMember > div.sCat + div.sContent')
                                  ->eq(3)
                                  ->getNode(0)
                                  ->firstChild;

        $assocNames = [];
        while (empty($currentElement) == false) {
            if (empty($currentElement->textContent) == false)
                array_push($assocNames, $currentElement->textContent);

            $currentElement = $currentElement->nextSibling;
        }

        return $assocNames;
    }

    public static function genres(Crawler $crawler)
    {
        $genreLinks = $crawler->filter('div.sContainer')
                                  ->eq(1) 
                                  ->filter('div.sMember > div.sCat + div.sContent')
                                  ->eq(1)
                                  ->filter('a > u');

        $genres = [];
        foreach ($genreLinks as $genreLink) {
            array_push($genres, $genreLink->textContent);
        }
        
        return $genres;
    }

    public static function authors(Crawler $crawler)
    {
        $authorLinks = $crawler->filter('div.sContainer')
                               ->eq(1) 
                               ->filter('div.sMember > div.sCat + div.sContent')
                               ->eq(5)
                               ->filter('a > u');

        $authors = [];
        foreach ($authorLinks as $authorLink) {
            array_push($authors, $authorLink->textContent);
        }

        return $authors;
    }

    public static function artists(Crawler $crawler)
    {
        $artistLinks = $crawler->filter('div.sContainer')
                               ->eq(1) 
                               ->filter('div.sMember > div.sCat + div.sContent')
                               ->eq(6)
                               ->filter('a > u');

        $artists = [];
        foreach ($artistLinks as $artistLink) {
            array_push($artists, $artistLink->textContent);
        }

        return $artists;
    }

    public static function year(Crawler $crawler)
    {
        $year = $crawler->filter('div.sContainer')
                        ->eq(1) 
                        ->filter('div.sMember > div.sCat + div.sContent')
                        ->eq(7)
                        ->getNode(0)
                        ->textContent;

        return intval($year);
    }

    public static function information_ex($mu_id, $contents)
    {
        $crawler = new Crawler($contents);

        $information['mu_id'] = $mu_id;
        $information['description'] = MangaUpdates::description($crawler);
        $information['type'] = MangaUpdates::type($crawler);
        $information['assoc_names'] = MangaUpdates::associated_names($crawler);
        $information['genres'] = MangaUpdates::genres($crawler);
        $information['authors'] = MangaUpdates::authors($crawler);
        $information['artists'] = MangaUpdates::artists($crawler);
        $information['year'] = MangaUpdates::year($crawler);

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
