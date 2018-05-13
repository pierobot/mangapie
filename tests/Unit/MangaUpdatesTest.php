<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\MangaUpdates;
use \App\IntlString;

class MangaUpdatesTest extends TestCase
{
    /**
     *  Asserts whether information extraction is successful or not.
     *
     *  @return void
     */
    public function testinformation_ex()
    {
        $contents = file_get_contents('tests/Data/MangaUpdates/Maison-Ikkoku.html');

        $information = MangaUpdates::information_ex(1051, $contents);

        $this->assertTrue(empty($information) === false);
        $this->assertTrue(array_key_exists('mu_id', $information));
        $this->assertTrue(array_key_exists('description', $information));
        $this->assertTrue(array_key_exists('type', $information));
        $this->assertTrue(array_key_exists('assoc_names', $information));
        $this->assertTrue(array_key_exists('genres', $information));
        $this->assertTrue(array_key_exists('authors', $information));
        $this->assertTrue(array_key_exists('artists', $information));
        $this->assertTrue(array_key_exists('year', $information));

        $this->assertTrue($information['mu_id'] == 1051);

        // $description = $information['description'];
        // $expected_description = 'Travel into Japan\'s nuttiest apartment house and meet its volatile inhabitants: Kyoko, the beautiful and mysterious new apartment manager; Yusaku, the exam-addled college student; Mrs. Ichinose, the drunken gossip; Kentaro, her bratty son; Akemi, the boozy bar hostess; and the mooching and peeping Mr. Yotsuya.';
        // $this->assertTrue(IntlString::strcmp($description, $expected_description) == 0);

        $type = $information['type'];
        $expected_type = 'Manga';
        $this->assertTrue(IntlString::strcmp($type, $expected_type) == 0);

        $assoc_names = $information['assoc_names'];
        $expected_assoc_names = [
            'Доходный дом Иккоку',
            'めぞん一刻',
            'Mezon Ikkoku'
        ];
        $this->assertTrue(empty($assoc_names) === false);
        foreach ($assoc_names as $index => $assoc_name) {

            $this->assertTrue(IntlString::strcmp($assoc_name, $expected_assoc_names[$index]) == 0);
        }

        $genres = $information['genres'];
        $expected_genres = [
            'Comedy',
            'Drama',
            'Romance',
            'Seinen',
            'Slice of Life'
        ];
        $this->assertTrue(empty($genres) === false);
        foreach ($genres as $index => $genre) {

            $this->assertTrue(IntlString::strcmp($genre, $expected_genres[$index]) == 0);
        }

        $authors = $information['authors'];
        $expected_authors = [ 'TAKAHASHI Rumiko' ];
        foreach ($authors as $index => $author) {

            $this->assertTrue(IntlString::strcmp($author, $expected_authors[$index]) == 0);
        }

        $artists = $information['artists'];
        $expected_artists = [ 'TAKAHASHI Rumiko' ];
        foreach ($artists as $index => $artist) {

            $this->assertTrue(IntlString::strcmp($artist, $expected_artists[$index]) == 0);
        }

        $year = $information['year'];
        $expected_year = '1980';
        $this->assertTrue(IntlString::strcmp($year, $expected_year) == 0);
    }

    /**
     *  Asserts whether or not we can correctly match a title that is several pages deep in search results.
     *  The first four pages, and first half of the fifth, are all yaoi doujinshi.
     *
     *  @return void
     */
    public function testsearch_ex()
    {
        $title = 'Yu Yu Hakusho';
        $page3_contents = file_get_contents('tests/Data/MangaUpdates/Yu-Yu-Hakusho-Page-3.json');

        $page3_results = MangaUpdates::search_ex($title, $page3_contents);

        $this->assertTrue(empty($page3_results) === false);

        foreach ($page3_results as $index => $result) {

            $this->assertTrue(array_key_exists('distance', $result));
            $this->assertTrue(array_key_exists('mu_id', $result));
            $this->assertTrue(array_key_exists('name', $result));
            $this->assertTrue(array_key_exists('url', $result));
        }

        // Jaro-Winkler distance should be 1.0 since they are equal
        $this->assertTrue($page3_results[0]['distance'] == 1.0);

        $this->assertTrue($page3_results[0]['mu_id'] == 118);

        $this->assertTrue(IntlString::strcmp($page3_results[0]['url'], 'https://www.mangaupdates.com/series.html?id=118') == 0);
    }
}
