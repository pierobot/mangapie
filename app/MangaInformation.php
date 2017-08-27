<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\Author;
use \App\AuthorReference;
use \App\Artist;
use \App\ArtistReference;
use \App\Genre;
use \App\GenreInformation;
use \App\Manga;
use \App\JaroWinkler;

class MangaInformation extends Model
{
    //
    protected $fillable = ['id', 'mu_id', 'name', 'description', 'type', 'year'];

    public function getMangaId() {

        return $this->id;
    }

    public function getMangaUpdatesId() {

        return $this->mu_id;
    }

    public function getDescription() {

        return $this->description;
    }

    public function getType() {

        return $this->type;
    }

    private static function searchMangaUpdates($name, $page = 1) {
        // https://www.mangaupdates.com/series.html?stype=title&search=asd&page=1&perpage=25
        $results = [];

        $file = \Curl::to('https://www.mangaupdates.com/series.html')->withData([

            'stype' => 'title',
            'search' => urlencode($name),
            'page' => strval($page),
            'perpage' => '25'
        ])->get();

        $a_elements = [];
        $a_element_count = preg_match_all('/<a href=(\"|\')https?:\/\/(www\.?)?mangaupdates\.com\/series\.html\?id=\d+(\"|\').+alt=(\"|\')Series Info(\"|\')>.+<\/a>/', $file, $a_elements);

        if ($a_element_count == 0 || $a_element_count === false)
            return $results;

        // index 0 contains the <a></a> element
        foreach ($a_elements[0] as $a_element) {

            $urls = [];
            $url_match_count = preg_match_all('/https?:\/\/(www)?\.mangaupdates\.com\/series.html\?id=\d+/', $a_element, $urls);
            if ($url_match_count == 0 || $url_match_count === false)
                continue;

            /* dd($urls);

                array:2 [▼
                  0 => array:1 [▼
                    0 => "https://www.mangaupdates.com/series.html?id=104755"
                  ],
                  1 => array:1 [▼
                    0 => "www"
                  ]
             */

            $names = [];
            $name_match_count = preg_match_all('/alt=(\"|\')Series Info(\"|\')>(<i>)?(.+?)(<\/i>)?<\/a>/', $a_element, $names);
            if ($name_match_count == 0 || $name_match_count === false)
                continue;

            /* dd($names);

                array:6 [▼
                  0 => array:1 [▼
                    0 => "alt='Series Info'>Yu Yu Hakusho dj - Shinkei ga Wareta Samui Yoru</a>"
                  ]
                  1 => array:1 [▼
                    0 => "'"
                  ]
                  2 => array:1 [▼
                    0 => "'"
                  ]
                  3 => array:1 [▼
                    0 => ""
                  ]
                  4 => array:1 [▼
                    0 => "Yu Yu Hakusho dj - Shinkei ga Wareta Samui Yoru"
                  ]
                  5 => array:1 [▼
                    0 => ""
                  ]
                ]
             */

            $ids = [];
            $id_match_count = preg_match_all('/\?id=(\d+)/', $urls[0][0], $ids);
            if ($id_match_count == 0 || $id_match_count === false)
                continue;

            /* dd($ids);

                array:2 [▼
                  0 => array:1 [▼
                    0 => "?id=104755"
                  ]
                  1 => array:1 [▼
                    0 => "104755"
                  ]
                ]
             */

            // see the comments above if you're confused about these two dimensional arrays and indices
            array_push($results, [
                'distance' => JaroWinkler::distance($name, $names[4][0]),
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
                return -1;
            elseif ($left['distance'] > $right['distance'])
                return 1;
        });

        return $results;
    }

    private static function getMangaUpdatesInformation($mu_id) {

        $file = \Curl::to('https://www.mangaupdates.com/series.html')->withData([

            'id' => $mu_id
        ])->get();

        $description = [];
        $description_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Description<\/b><\/div>\s<div class=(\"|\')sContent(\"|\').+\">(.+?)\s<\/div>/', $file, $description);
        if ($description_result == 0 || $description_result === false)
            return null;

        /* dd($description);

            array:6 [▼
              0 => array:1 [▼
                0 => """
                  <div class="sCat"><b>Description</b></div>\n
                  <div class="sContent" style="text-align:justify">Watanuki Kimihiro is haunted by visions of ghosts and spirits. Seemingly by chance, he encounters a mysterious witch named Y&ucirc;ko, who claims she can help. In desperation, he accepts, but realizes that he&#039;s just been tricked into working for Y&ucirc;ko in order to pay off the cost of her services. Soon he&#039;s employed in her little shop- a job which turns out to be nothing like his previous work experience!\n
                  </div>
                  """
              ]
              1 => array:1 [▼
                0 => """
              ]
              2 => array:1 [▼
                0 => """
              ]
              3 => array:1 [▼
                0 => """
              ]
              4 => array:1 [▼
                0 => """
              ]
              5 => array:1 [▼
                0 => "Watanuki Kimihiro is haunted by visions of ghosts and spirits. Seemingly by chance, he encounters a mysterious witch named Y&ucirc;ko, who claims she can help. In desperation, he accepts, but realizes that he&#039;s just been tricked into working for Y&ucirc;ko in order to pay off the cost of her services. Soon he&#039;s employed in her little shop- a job which turns out to be nothing like his previous work experience!"
              ]
            ]

         */

        $type = [];
        $type_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Type<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s+>(.+?)\s<\/div>/', $file, $type);
        if ($type_result == 0 || $type_result === false)
            return null;

        /* dd($type);

            array:6 [▼
              0 => array:1 [▼
                0 => """
                  <div class="sCat"><b>Type</b></div>\n
                  <div class="sContent" >Manga\n
                  </div>
                  """
              ]
              1 => array:1 [▼
                0 => """
              ]
              2 => array:1 [▼
                0 => """
              ]
              3 => array:1 [▼
                0 => """
              ]
              4 => array:1 [▼
                0 => """
              ]
              5 => array:1 [▼
                0 => "Manga"
              ]
            ]

         */

        $assoc_names_content = [];
        $assoc_names_content_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Associated Names<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s>(.+?)\s<\/div>/', $file, $assoc_names_content);
        if ($assoc_names_content_result == 0 || $assoc_names_content_result === false)
            return null;

        /* dd($assoc_names_content_result);

            array:6 [▼
              0 => array:1 [▼
                0 => """
                  <div class="sCat"><b>Associated Names</b></div>\n
                  <div class="sContent" >&#1058;&#1088;&#1080;&#1087;&#1083;&#1077;&#1082;&#1089;&#1086;&#1075;&#1086;&#1083;&#1080;&#1082;<br />&times;&times;&times;HOLiC<br />&times;&times;&times;HOLiC&#12539;&#31840;<br />Holic<br />XXX &#12507;&#12522;&#12483;&#12463;<br />xxxHolic R&#333;<br />xxxHolic Rou<br />\n
                  </div>
                  """
              ]
              1 => array:1 [▼
                0 => """
              ]
              2 => array:1 [▼
                0 => """
              ]
              3 => array:1 [▼
                0 => """
              ]
              4 => array:1 [▼
                0 => """
              ]
              5 => array:1 [▼
                0 => "&#1058;&#1088;&#1080;&#1087;&#1083;&#1077;&#1082;&#1089;&#1086;&#1075;&#1086;&#1083;&#1080;&#1082;<br />&times;&times;&times;HOLiC<br />&times;&times;&times;HOLiC&#12539;&#31840;<br />Holic<br />XXX &#12507;&#12522;&#12483;&#12463;<br />xxxHolic R&#333;<br />xxxHolic Rou<br />"
              ]
            ]

         */

        $assoc_names = [];
        $assoc_names_result = preg_match_all('/(\&\#\d+\;)?(.+?)(<br\s\/>)/', $assoc_names_content[5][0], $assoc_names);
        if ($assoc_names_result == 0 || $assoc_names_result === false)
            return null;

        // the names are url encoded. take care of that.
        array_walk($assoc_names[2], function (&$raw_name, $key) {

            $raw_name = urldecode($raw_name);
        }, null);

        /* dd($assoc_names);

            array:4 [
              0 => array:7 [
                0 => "&#1058;&#1088;&#1080;&#1087;&#1083;&#1077;&#1082;&#1089;&#1086;&#1075;&#1086;&#1083;&#1080;&#1082;<br />"
                1 => "&times;&times;&times;HOLiC<br />"
                2 => "&times;&times;&times;HOLiC&#12539;&#31840;<br />"
                3 => "Holic<br />"
                4 => "XXX &#12507;&#12522;&#12483;&#12463;<br />"
                5 => "xxxHolic R&#333;<br />"
                6 => "xxxHolic Rou<br />"
              ]
              1 => array:7 [
                0 => "&#1058;"
                1 => ""
                2 => ""
                3 => ""
                4 => ""
                5 => ""
                6 => ""
              ]
              2 => array:7 [
                0 => "&#1088;&#1080;&#1087;&#1083;&#1077;&#1082;&#1089;&#1086;&#1075;&#1086;&#1083;&#1080;&#1082;"
                1 => "&times;&times;&times;HOLiC"
                2 => "&times;&times;&times;HOLiC&#12539;&#31840;"
                3 => "Holic"
                4 => "XXX &#12507;&#12522;&#12483;&#12463;"
                5 => "xxxHolic R&#333;"
                6 => "xxxHolic Rou"
              ]
              3 => array:7 [
                0 => "<br />"
                1 => "<br />"
                2 => "<br />"
                3 => "<br />"
                4 => "<br />"
                5 => "<br />"
                6 => "<br />"
              ]
            ]

         */

        $genres = [];
        $genres_result = preg_match_all('/<a rel=(\"|\').+?genre=.+?<u>(.+?)<\/u><\/a>/', $file, $genres);
        if ($genres_result == 0 || $genres_result === false)
            return null;

        /* dd($genres);

            array:3 [▼
              0 => array:8 [▼
                0 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Comedy'><u>Comedy</u></a>"
                1 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Drama'><u>Drama</u></a>"
                2 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Fantasy'><u>Fantasy</u></a>"
                3 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Mystery'><u>Mystery</u></a>"
                4 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Psychological'><u>Psychological</u></a>"
                5 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=School+Life'><u>School Life</u></a>"
                6 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Seinen'><u>Seinen</u></a>"
                7 => "<a rel="nofollow" href='https://www.mangaupdates.com/series.html?act=genresearch&amp;genre=Supernatural'><u>Supernatural</u></a>"
              ]
              1 => array:8 [▼
                0 => """
                1 => """
                2 => """
                3 => """
                4 => """
                5 => """
                6 => """
                7 => """
              ]
              2 => array:8 [▼
                0 => "Comedy"
                1 => "Drama"
                2 => "Fantasy"
                3 => "Mystery"
                4 => "Psychological"
                5 => "School Life"
                6 => "Seinen"
                7 => "Supernatural"
              ]
            ]

         */
         

        $authors_content = [];
        $authors_content_result = preg_match_all('/Author\(s\)<\/b><\/div>\s.+?sContent(\"|\')\s>.+?\s<\/div>/', $file, $authors_content);
        if ($authors_content_result == 0 || $authors_content_result === false)
            return null;

        /* dd($authors_content);

            array:2 [
              0 => array:1 [▼
                0 => """
                  Author(s)</b></div>\n
                  <div class="sContent" ><a href='https://www.mangaupdates.com/authors.html?id=208' title='Author Info'><u>CLAMP</u></a><BR><a href='https://www.mangaupdates.com/authors.html?id=139' title='Author Info'><u>OHKAWA Ageha</u></a><BR>\n
                  </div>
                  """
              ]
              1 => array:1 [▼
                0 => """
              ]
            ]

         */

        $authors = [];
        $authors_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $authors_content[0][0], $authors);
        if ($authors_result == 0 || $authors_result === false)
            return null;

        /* dd($authors);

            array:3 [
              0 => array:2 [▼
                0 => "?id=208' title='Author Info'><u>CLAMP</u>"
                1 => "?id=139' title='Author Info'><u>OHKAWA Ageha</u>"
              ]
              1 => array:2 [▼
                0 => "208"
                1 => "139"
              ]
              2 => array:2 [▼
                0 => "CLAMP"
                1 => "OHKAWA Ageha"
              ]
            ]

         */

        $artists_content = [];
        $artists_content_result = preg_match_all('/(Artist\(s\)<\/b><\/div>\s.+?sContent(\"|\')\s>).+?\s<\/div>/', $file, $artists_content);
        if ($artists_content_result == 0 || $artists_content_result === false)
            return null;

        /* dd($artists_content);

            array:3 [
              0 => array:1 [
                0 => """
                  Artist(s)</b></div>\n
                  <div class="sContent" ><a href='https://www.mangaupdates.com/authors.html?id=1941' title='Author Info'><u>APAPA Mokona</u></a><BR><a href='https://www.mangaupdates.com/authors.html?id=208' title='Author Info'><u>CLAMP</u></a><BR><a href='https://www.mangaupdates.com/authors.html?id=2461' title='Author Info'><u>NEKOI Tsubaki</u></a><BR>\n
                  </div>
                  """
              ]
              1 => array:1 [
                0 => """
                  Artist(s)</b></div>\n
                  <div class="sContent" >
                  """
              ]
              2 => array:1 [
                0 => """
              ]
            ]

         */

        $artists = [];
        $artists_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $artists_content[0][0], $artists);
        if ($artists_result == 0 || $artists_result === false)
            return null;

        /* dd($artists);

            array:3 [
              0 => array:3 [
                0 => "?id=1941' title='Author Info'><u>APAPA Mokona</u>"
                1 => "?id=208' title='Author Info'><u>CLAMP</u>"
                2 => "?id=2461' title='Author Info'><u>NEKOI Tsubaki</u>"
              ]
              1 => array:3 [
                0 => "1941"
                1 => "208"
                2 => "2461"
              ]
              2 => array:3 [
                0 => "APAPA Mokona"
                1 => "CLAMP"
                2 => "NEKOI Tsubaki"
              ]
            ]

         */

        $year = [];
        $year_result = preg_match_all('/(\"|\')sCat(\"|\')><b>Year<\/b><\/div>\s<div class=(\"|\')sContent(\"|\')\s>(\d+)\s<\/div>/', $file, $year);
        if ($year_result == 0 || $year_result === false)
            return null;

        /* dd(year);

            array:6 [
              0 => array:1 [
                0 => """
                  "sCat"><b>Year</b></div>\n
                  <div class="sContent" >2003\n
                  </div>
                  """
              ]
              1 => array:1 [
                0 => """
              ]
              2 => array:1 [
                0 => """
              ]
              3 => array:1 [
                0 => """
              ]
              4 => array:1 [
                0 => """
              ]
              5 => array:1 [
                0 => "2003"
              ]
            ]

         */


        // see the comments above if you're confused about these two dimensional arrays and indices
        $information['mu_id'] = $mu_id;
        $information['description'] = $description[5][0];
        $information['type'] = $type[5][0];
        $information['assoc_names'] = $assoc_names[2];
        $information['genres'] = $genres[2];
        $information['authors'] = $authors[2];
        $information['artists'] = $artists[2];
        $information['year'] = $year[5][0];

        return $information;
    }

    private function updateMangaInformation($mu_info) {
        if ($mu_info == null)
            return false;

        // $this->mu_id = $mu_info['mu_id'];
        // $this->description = $mu_info['description'];
        // $this->type = $mu_info['type'];
        // $this->year = $mu_info['year'];

        $this->update([
            'mu_id' => $mu_info['mu_id'],
            'description' => $mu_info['description'],
            'type' => $mu_info['type'],
            'year' => $mu_info['year']
        ]);

        //$this->save();

        return true;
    }

    private function updateAssociatedNames($mu_info) {
        if ($mu_info == null)
            return false;

        $references = AssociatedNameReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('assoc_names', $mu_info) == false)
            return false;

        foreach ($mu_info['assoc_names'] as $name) {
            $assoc_name = AssociatedName::create([
                'name' => $name
            ]);

            $reference = AssociatedNameReference::create([
                'manga_id' => $this->getMangaId(),
                'assoc_name_id' => $assoc_name->getId()
            ]);
        }

        return true;
    }

    private function updateGenreInformation($mu_info) {
        if ($mu_info == null)
            return false;

        $genre_info = GenreInformation::where('manga_id', '=', $this->getMangaId());
        $genre_info->forceDelete();

        if (array_key_exists('genres', $mu_info) == false)
            return false;

        for ($i = 0; $i < sizeof($mu_info['genres']); $i++) {
            $genre_name = $mu_info['genres'][$i];
            $genre = Genre::where('name', '=', $genre_name)->first();

            GenreInformation::updateOrCreate([
                'manga_id' => $this->getMangaId(),
                'genre_id' => $genre->getId()
            ]);
        }

        return true;
    }

    private function updateArtistsInformation($mu_info) {
        if ($mu_info == null)
            return false;
        
        $references = ArtistReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('artists', $mu_info) == false)
            return false;

        for ($i = 0; $i < sizeof($mu_info['artists']); $i++) {
            $artist_name = $mu_info['artists'][$i];
            
            $artist = Artist::create([
                'name' => $artist_name
            ]);

            $reference = ArtistReference::create([
                'manga_id' => $this->getMangaId(),
                'artist_id' => $artist->getId()
            ]);
        }

        return true;
    }

    private function updateAuthorsInformation($mu_info) {
        if ($mu_info == null)
            return false;

        $references = AuthorReference::where('manga_id', '=', $this->getMangaId());
        $references->forceDelete();

        if (array_key_exists('authors', $mu_info) == false)
            return false;

        for ($i = 0; $i < sizeof($mu_info['authors']); $i++) {
            $author_name = $mu_info['authors'][$i];
            
            $author = Author::create([
                'name' => $author_name
            ]);

            $reference = AuthorReference::create([
                'manga_id' => $this->getMangaId(),
                'author_id' => $author->getId()
            ]);
        }

        return true;
    }

    public static function createFromMangaUpdates($id, $name) {
        $manga_info = null;

        $search_results = [];
        $top_match = null;
        // search through five pages
        for ($i = 1; $i <= 5; $i++) {

            $results = MangaInformation::searchMangaUpdates($name, $i);

            // abort if the search fails
            if (count($results) == 0)
                return null;

            // avoid getting other pages if we have a perfect match
            if ($results[0]['distance'] == 1.0) {

                $top_match = $results[0];
                break;
            }

            // a perfect match wasn't found, just append
            foreach ($results as $result) {

                array_push($search_results, $result);
            }
        }

        if ($top_match == null) {
            // sort descending
            usort($search_results, function ($left, $right) {
                if ($left['distance'] == $right['distance'])
                    return 0;
                elseif ($left['distance'] < $right['distance'])
                    return 1;
                elseif ($left['distance'] > $right['distance'])
                    return -1;
            });

            if (count($search_results) > 0) {
                $top_match = $search_results[0];
            }
        }

        if ($top_match != null) {

            $mu_id = $top_match['mu_id'];
            $mu_info = MangaInformation::getMangaUpdatesInformation($mu_id);

            if ($mu_info != null) {
                MangaInformation::create([
                    'id' => $id
                ]);

                $manga_info = MangaInformation::find($id);

                if ($manga_info->updateMangaInformation($mu_info) === false)
                    return null;

                if ($manga_info->updateAssociatedNames($mu_info) == false)
                    return null;

                if ($manga_info->updateGenreInformation($mu_info) === false)
                    return null;

                if ($manga_info->updateArtistsInformation($mu_info) == false)
                    return null;

                if ($manga_info->updateAuthorsInformation($mu_info) == false)
                    return null;

                $manga_info->save();
            }
        }

        return $manga_info;
    }

    public function updateFromMangaUpdates($mu_id) {
        // Update the values that don't depend on mangaupdates first
        // ...

        // Update those that depend on mangaupdates
        $mu_info = $this->getMangaUpdatesInformation($mu_id);
        if ($mu_info == null)
            return false;    

        if ($this->updateMangaInformation($mu_info) === false)
            return false;

        if ($this->updateAssociatedNames($mu_info) == false)
            return false;

        if ($this->updateGenreInformation($mu_info) === false)
            return false;

        if ($this->updateArtistsInformation($mu_info) == false)
            return false;

        if ($this->updateAuthorsInformation($mu_info) == false)
            return false;

        return true;
    }

    public function getAssociatedNames() {
        $assoc_names = [];
        $name_references = AssociatedNameReference::where('manga_id', '=', $this->getMangaId())->get();

        if ($name_references == null)
            return null;

        foreach ($name_references as $reference) {
            $assoc_name = AssociatedName::find($reference->getAssociatedNameId());
            if ($assoc_name == null)
                continue;

            array_push($assoc_names, $assoc_name);
        }

        return $assoc_names != [] ? $assoc_names : null;
    }

    public function getYear() {
        return $this->year;
    }

    public function getGenres() {
        $genres = [];
        $genre_info = GenreInformation::where('manga_id', '=', $this->getMangaId())->get();

        if ($genre_info == null)
            return null;

        for ($i = 0; $i < sizeof($genre_info); $i++) {
            $genre = Genre::find($genre_info[$i]['genre_id']);
            if ($genre == null)
                continue;

            $genres[$i] = $genre->getName();
        }

        return $genres != [] ? $genres : null;
    }

    public function getAuthors() {
        $references = AuthorReference::where('manga_id', '=', $this->getMangaId())->get();
        $authors = [];

        foreach ($references as $reference) {
            $author = Author::find($reference->getAuthorId());
            if ($author != null)
                array_push($authors, $author);
        }

        return $authors != [] ? $authors : null;
    }

    public function getArtists() {
        $references = ArtistReference::where('manga_id', '=', $this->getMangaId())->get();
        $artists = [];

        foreach ($references as $reference) {
            $artist = Artist::find($reference->getArtistId());
            if ($artist != null)
                array_push($artists, $artist);
        }

        return $artists != [] ? $artists : null;
    }
}
