<?php

namespace App\Sources\MangaUpdates;

use App\IntlString;
use App\JaroWinkler;

use Symfony\Component\DomCrawler\Crawler;

class Series
{
//    private static function encodeNCR($str)
//    {
//        $result = '';
//
//        $length = IntlString::strlen($str);
//        for ($i = 0, $offset = 0, $next = 0; $i < $length; $i++) {
//            $grapheme = IntlString::grapheme($str, $offset, $next);
//            $codepoint = mb_ord($grapheme, 'UTF-8');
//
//            // If the code point is not ASCII then we have to append &#codepoint;
//            $result .= $codepoint > 255 ? ('&#' . $codepoint . ';') : $grapheme;
//
//            $offset = $next;
//        }
//
//        return $result;
//    }

    /**
     * Searches mangaupdates for the given title.
     * The results are sorted by jaro winkler distance from highest to lowest.
     *
     * @param string $title The title of the manga.
     * @param int $pageLimit The number of pages to limit the search by.
     * @param int $perPage The number of results to request per page.
     *
     * @return array A two dimensional array where the second has keys 'mu_id', 'url', 'name', and 'distance'.
     */
    public static function search(string $title, int $pageLimit = 3, int $perPage = 50)
    {
        $results = [];

        for ($i = 1, $found = false; $i <= $pageLimit && $found == false; $i++) {
            $contents = \Curl::to('https://www.mangaupdates.com/series.html')->withData([
                'stype' => 'title',
                'search' => $title,
                'page' => strval($i),
                'perpage' => strval($perPage),
                'output' => 'json'
            ])->post();

            $pageResults = self::collectPageResults($title, $contents);
            foreach ($pageResults as $pageResult) {
                if ($pageResult['distance'] == 1.0)
                    $found = true;
            }

            $results = array_merge($results, $pageResults);
        }

        // sort based on jaro-winkler distance
        usort($results, function ($left, $right) {
            $leftDistance = $left['distance'];
            $rightDistance = $right['distance'];

            return $leftDistance == $rightDistance ? 0 : ($leftDistance < $rightDistance ? 1 : -1);
        });

        return $results;
    }

    private static function collectPageItems($title, $items)
    {
        $results = [];

        foreach ($items as $item) {
            $mu_id = intval($item->{'id'});
            $url = 'https://www.mangaupdates.com/series.html?id=' . $item->{'id'};
            $item_title = \Html::decode($item->{'title'});
            $distance = JaroWinkler::distance($title, $item_title);

            $results[] = [
                'mu_id' => $mu_id,
                'url' => $url,
                'name' => $item_title,
                'distance' => $distance
            ];
        }

        return $results;
    }

    private static function collectPageResults($title, $contents)
    {
        $results = [];

        $json = json_decode($contents);
        if (empty($json) == false &&
            empty($json->{'results'}) == false &&
            empty($json->{'results'}->{'items'}) == false) {

            $items = $json->{'results'}->{'items'};

            $results = self::collectPageItems($title, $items);
        }

        return $results;
    }

    private static function description(Crawler $crawler)
    {
        return trim($crawler->filter('div.sMember > div.sCat + div.sContent')
            ->eq(0)
            ->text());
    }

    private static function type(Crawler $crawler)
    {
        return trim($crawler->filter('div.sMember > div.sCat + div.sContent')
            ->eq(1)
            ->text());
    }

    private static function associated_names(Crawler $crawler)
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
                $assocNames[] = $currentElement->textContent;

            $currentElement = $currentElement->nextSibling;
        }

        return $assocNames;
    }

    private static function genres(Crawler $crawler)
    {
        $genreLinks = $crawler->filter('div.sContainer')
            ->eq(1)
            ->filter('div.sMember > div.sCat + div.sContent')
            ->eq(1)
            ->filter('a > u');

        $genres = [];
        foreach ($genreLinks as $genreLink) {
            $genres[] = $genreLink->textContent;
        }

        return $genres;
    }

    private static function authors(Crawler $crawler)
    {
        $authorLinks = $crawler->filter('div.sContainer')
            ->eq(1)
            ->filter('div.sMember > div.sCat + div.sContent')
            ->eq(5)
            ->filter('a > u');

        $authors = [];
        foreach ($authorLinks as $authorLink) {
            $authors[] = $authorLink->textContent;
        }

        return $authors;
    }

    private static function artists(Crawler $crawler)
    {
        $artistLinks = $crawler->filter('div.sContainer')
            ->eq(1)
            ->filter('div.sMember > div.sCat + div.sContent')
            ->eq(6)
            ->filter('a > u');

        $artists = [];
        foreach ($artistLinks as $artistLink) {
            $artists[] = $artistLink->textContent;
        }

        return $artists;
    }

    private static function year(Crawler $crawler)
    {
        $year = $crawler->filter('div.sContainer')
            ->eq(1)
            ->filter('div.sMember > div.sCat + div.sContent')
            ->eq(7)
            ->getNode(0)
            ->textContent;

        return intval($year);
    }

    private static function collectInformation($mu_id, $contents)
    {
        $crawler = new Crawler($contents);

        $information['mu_id'] = $mu_id;
        $information['description'] = self::description($crawler);
        $information['type'] = self::type($crawler);
        $information['assoc_names'] = self::associated_names($crawler);
        $information['genres'] = self::genres($crawler);
        $information['authors'] = self::authors($crawler);
        $information['artists'] = self::artists($crawler);
        $information['year'] = self::year($crawler);

        return $information;
    }

    public static function information($mu_id)
    {
        $contents = \Curl::to('https://www.mangaupdates.com/series.html')->withData([
            'id' => $mu_id
        ])->get();

        return self::collectInformation($mu_id, $contents);
    }
}