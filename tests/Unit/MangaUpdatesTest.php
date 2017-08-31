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
     *  Asserts whether genre extraction is successful or not.
     *
     * @return void
     */
    public function testgenres_ex()
    {
        $contents = file_get_contents('tests/Data/MangaUpdates/Genres.html');

        $expected_names = [
            'Action',
            'Adult',
            'Adventure',
            'Comedy',
            'Doujinshi',
            'Drama',
            'Ecchi',
            'Fantasy',
            'Gender Bender',
            'Harem',
            'Hentai',
            'Historical',
            'Horror',
            'Josei',
            'Lolicon',
            'Martial Arts',
            'Mature',
            'Mecha',
            'Mystery',
            'Psychological',
            'Romance',
            'School Life',
            'Sci-fi',
            'Seinen',
            'Shotacon',
            'Shoujo',
            'Shoujo Ai',
            'Shounen',
            'Shounen Ai',
            'Slice of Life',
            'Smut',
            'Sports',
            'Supernatural',
            'Tragedy',
            'Yaoi',
            'Yuri'
        ];

        $expected_descriptions = [
            'A work typically depicting fighting, violence, chaos, and fast paced motion.',
            'Contains content that is suitable only for adults. Titles in this category may include prolonged scenes of intense violence and/or graphic sexual content and nudity.',
            'If a character in the story goes on a trip or along that line, your best bet is that it is an adventure manga.  Otherwise, it\'s up to your personal prejudice on this case.',
            'A dramatic work that is light and often humorous or satirical in tone and that usually contains a happy resolution of the thematic conflict.',
            'Fan based work inspired by official anime or manga. For MangaUpdates, original works DO NOT fall under this category',
            'A work meant to bring on an emotional response, such as instilling sadness or tension.',
            'Possibly the line between hentai and non-hentai, ecchi usually refers to fanservice put in to attract a certain group of fans.',
            'Anything that involves, but not limited to, magic, dream world, and fairy tales.',
            'Girls dressing up as guys, guys dressing up as girls.. Guys turning into girls, girls turning into guys.. I think you get the picture.',
            'A series involving one male character and many female characters (usually attracted to the male character). A Reverse Harem is when the genders are reversed.',
            'Adult sexual content in an illustrated form where the FOCUS of the manga is placed on sexually graphic acts.',
            'Having to do with old or ancient times.',
            'A painful emotion of fear, dread, and abhorrence; a shuddering with terror and detestation; the feeling inspired by something frightful and shocking.',
            'Literally "Woman". Targets women 18-30. Female equivalent to seinen. Unlike shoujo the romance is more realistic and less idealized. The storytelling is more explicit and mature.',
            'Representing a sexual attraction to young or under-age girls.',
            'As the name suggests, anything martial arts related. Any of several arts of combat or self-defense, such as aikido, karate, judo, or tae kwon do, kendo, fencing, and so on and so forth.',
            'Contains subject matter which may be too extreme for people under the age of 17. Titles in this category may contain intense violence, blood and gore, sexual content and/or strong language.',
            'A work involving and usually concentrating on all types of large robotic machines.',
            'Usually an unexplained event occurs, and the main protagonist attempts to find out what caused it.',
            'Usually deals with the philosophy of a state of mind, in most cases detailing abnormal psychology.',
            'Any love related story.  We will define love as between man and woman in this case. Other than that, it is up to your own imagination of what love is.',
            'Having a major setting of the story deal with some type of school.',
            'Short for science fiction, these works involve twists on technology and other science related phenomena which are contrary or stretches of the modern day scientific world.',
            'From Google:<BR>Seinen means "young Man." Manga and anime that specifically targets young adult males around the ages of 18 to 25 are seinen titles. The stories in seinen works appeal to university students and those in the working world. Typically the story lines deal with the issues of adulthood.',
            'Representing a sexual attraction to young or under-age boys.',
            'A work intended and primarily written for females.  Usually involves a lot of romance and strong character development.',
            'Often synonymous with yuri, this can be thought of as somewhat less extreme.  "Girl\'s Love", so to speak.',
            'A work intended and primarily written for males.  These  works usually involve fighting and/or violence.',
            'Often synonymous with yaoi, this can be thought of as somewhat less extreme.  "Boy\'s Love"�, so to speak.',
            'As the name suggests, this genre represents day-to-day tribulations of one/many character(s). These challenges/events could technically happen in real life and are often -if not all the time- set in the present timeline in a world that mirrors our own.',
            'Deals with series that are considered profane or offensive, particularly with regards to sexual content',
            'As the name suggests, anything sports related.  Baseball, basketball, hockey, soccer, golf, and racing just to name a few.',
            'Usually entails amazing and unexplained powers or events which defy the laws of physics.',
            'Contains events resulting in great loss and misfortune.',
            'This work usually involves intimate relationships between men.',
            'This work usually involves intimate relationships between women.',
        ];

        $genres = MangaUpdates::genres_ex($contents);

        $this->assertTrue(empty($genres) === false);
        $this->assertTrue(count($genres) == count($expected_names));

        foreach ($genres as $index => $genre) {

            $this->assertTrue(array_key_exists('name', $genre) === true);
            $this->assertTrue(array_key_exists('description', $genre) === true);

            $genre_name = $genre['name'];
            $genre_expected_name = IntlString::convert($expected_names[$index]);

            $this->assertTrue(IntlString::strcmp($genre_name, $genre_expected_name) == 0);

            $genre_desc = $genre['description'];
            $genre_expected_desc = IntlString::convert(\Html::decode($expected_descriptions[$index]));

            $this->assertTrue(IntlString::strcmp($genre_desc, $genre_expected_desc) == 0);
        }
    }

    /**
     *  Asserts whether information extraction is successful or not.
     *
     *  @return void
     */
    public function testinformation_ex()
    {
        $contents = file_get_contents('tests/Data/MangaUpdates/Maison Ikkoku.html');

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
        $page4_contents = file_get_contents('tests/Data/MangaUpdates/Yu Yu Hakusho-Page-4.html');
        $page5_contents = file_get_contents('tests/Data/MangaUpdates/Yu Yu Hakusho-Page-5.html');

        $page4_expected_names = [
            'Yu Yu Hakusho dj - Dune',
            'Yu Yu Hakusho dj - Goldfish',
            'Yu Yu Hakusho dj - Higeki',
            'Yu Yu Hakusho dj - Himitsu',
            'Yu Yu Hakusho dj - Hiruiseki',
            'Yu Yu Hakusho dj - Hisoka',
            'Yu Yu Hakusho dj - Hiyaku',
            'Yu Yu Hakusho dj - Hourglass',
            'Yu Yu Hakusho dj - Howling',
            'Yu Yu Hakusho dj - Inyoku',
            'Yu Yu Hakusho dj - It must be love',
            'Yu Yu Hakusho dj - Jealousy',
            'Yu Yu Hakusho dj - Joy',
            'Yu Yu Hakusho dj - Kingyo',
            'Yu Yu Hakusho dj - Narukami',
            'Yu Yu Hakusho dj - Nue',
            'Yu Yu Hakusho dj - Opium',
            'Yu Yu Hakusho dj - Our Babies',
            'Yu Yu Hakusho dj - Overture',
            'Yu Yu Hakusho dj - P0rn0graphy',
            'Yu Yu Hakusho dj - Paradise',
            'Yu Yu Hakusho dj - POrnOgraphy',
            'Yu Yu Hakusho dj - Psycho',
            'Yu Yu Hakusho dj - Purification',
            'Yu Yu Hakusho dj - Romantic'
        ];

        $page5_expected_names = [
            'Yu Yu Hakusho dj - San',
            'Yu Yu Hakusho dj - Self Control',
            'Yu Yu Hakusho dj - Shogun',
            'Yu Yu Hakusho dj - Sickness',
            'Yu Yu Hakusho dj - SM28',
            'Yu Yu Hakusho dj - You Brute',
            'Yu Yu Hakusho dj - Yumegatari',
            'Yu Yu Hakusho dj - Getting Used to It',
            'Yu Yu Hakusho dj - With me',
            'Yu Yu Hakusho',
            'Mowang Yu Yongzhe Yu Sheng Jian Shendian',
            'No No Yu',
            'Yu',
            'Bijutsubu Yu-re-',
            'Crisis Yu',
            'Ling Yu (Novel)',
            'Little Yu (French)',
            'Nobose Yu',
            'Nono Yu',
            'Qing Yu (Novel)',
            'To-yu-ki',
            'Tsuyako no Yu',
            'Xin x Yu',
            'Yu Ai',
            'Yu Dan'
        ];

        $page4_results = MangaUpdates::search_ex($title, $page4_contents);
        $page5_results = MangaUpdates::search_ex($title, $page5_contents);

        $this->assertTrue(empty($page4_results) === false);

        foreach ($page4_results as $index => $result) {

            $this->assertTrue(array_key_exists('distance', $result));
            $this->assertTrue(array_key_exists('mu_id', $result));
            $this->assertTrue(array_key_exists('name', $result));
            $this->assertTrue(array_key_exists('url', $result));

            // Yu Yu Hakusho should not be in this page
            $this->assertTrue(IntlString::strcmp($result['name'], $title) != 0);
        }

        foreach ($page5_results as $index => $result) {

            $this->assertTrue(array_key_exists('distance', $result));
            $this->assertTrue(array_key_exists('mu_id', $result));
            $this->assertTrue(array_key_exists('name', $result));
            $this->assertTrue(array_key_exists('url', $result));

            // Yu Yu Hakusho should be in this page
            if (IntlString::strcmp($result['name'], $title) == 0) {

                break;
            }
        }

        $this->assertTrue(IntlString::strcmp($result['name'], $title) == 0);

        // Jaro-Winkler distance should be 1.0 since they are equal
        $this->assertTrue($page5_results[0]['distance'] == 1.0);

        $this->assertTrue($page5_results[0]['mu_id'] == 118);

        $this->assertTrue(IntlString::strcmp($page5_results[0]['url'], 'https://www.mangaupdates.com/series.html?id=118') == 0);
    }
}
