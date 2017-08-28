<?php

namespace App;

use \App\IntlString;

class MangaUpdates {

    public static function genres() {

        $file = \Curl::to('https://www.mangaupdates.com/genres.html')->get();

        $genres_info = [];
        $genres_info_result = preg_match_all('/(<td class=(\"|\')releasestitle(\"|\').+?<b>(.+?)<\/b><\/td>)\s.+\s.+?\s.+?(<td class=(\"|\')text(\"|\') align=(\"|\').+?(\"|\')>(.+?)<br>)/', $file, $genres_info);
        if ($genres_info_result == 0 || $genres_info_result === false)
            return null;

    /* dd($genres_info);

        array:11 [▼
          ...
          4 => array:36 [▼
            0 => "Action"
            1 => "Adult"
            2 => "Adventure"
            3 => "Comedy"
            5 => "Drama"
            7 => "Fantasy"
            ...
          ]
          ...
          10 => array:36 [▼
            0 => "A work typically depicting fighting, violence, chaos, and fast paced motion."
            1 => "Contains content that is suitable only for adults. Titles in this category may include prolonged scenes of intense violence and/or graphic sexual content and nu ▶"
            2 => "If a character in the story goes on a trip or along that line, your best bet is that it is an adventure manga.  Otherwise, it&#039;s up to your personal prejudi ▶"
            3 => "A dramatic work that is light and often humorous or satirical in tone and that usually contains a happy resolution of the thematic conflict."
            5 => "A work meant to bring on an emotional response, such as instilling sadness or tension."
            7 => "Anything that involves, but not limited to, magic, dream world, and fairy tales."
            ...
          ]
        ]
     */

        // see the comment above if you're confused about these two dimensional arrays and indices
        $genres = [];
        foreach ($genres_info[4] as $index => $genre_name) {

            array_push($genres, [
                'name' => $genre_name,
                'description' => IntlString::convert(urldecode($genres_info[10][$index]))
            ]);
        }

        return $genres;
    }

    public static function search($title, $page, $perpage = 25) {

    	$results = [];
        // https://www.mangaupdates.com/series.html?stype=title&search=asd&page=1&perpage=25

        $file = \Curl::to('https://www.mangaupdates.com/series.html')->withData([

            'stype' => 'title',
            'search' => urlencode($title),
            'page' => strval($page),
            'perpage' => strval($perpage)
        ])->get();

        $a_elements = [];
        $a_element_count = preg_match_all('/<a href=(\"|\')https?:\/\/(www\.?)?mangaupdates\.com\/series\.html\?id=\d+(\"|\').+alt=(\"|\')Series Info(\"|\')>.+<\/a>/', $file, $a_elements);

        if ($a_element_count == 0 || $a_element_count === false)
            return null;

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
                  ...
             */

            $names = [];
            $name_match_count = preg_match_all('/alt=(\"|\')Series Info(\"|\')>(<i>)?(.+?)(<\/i>)?<\/a>/', $a_element, $names);
            if ($name_match_count == 0 || $name_match_count === false)
                continue;

            // url decode the names
            array_walk($names[4], function (&$name, $key) {

            	$name = IntlString::convert(urldecode($name));
            });

            /* dd($names);

                array:6 [▼
 				  ...
                  4 => array:1 [▼
                    0 => "Yu Yu Hakusho dj - Shinkei ga Wareta Samui Yoru"
                  ]
                  ...
                ]
             */

            $ids = [];
            $id_match_count = preg_match_all('/\?id=(\d+)/', $urls[0][0], $ids);
            if ($id_match_count == 0 || $id_match_count === false)
                continue;

            /* dd($ids);

                array:2 [▼
                  ...
                  1 => array:1 [▼
                    0 => "104755"
                  ]
                ]
             */

            // see the comments above if you're confused about these two dimensional arrays and indices
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

    public static function information($mu_id) {

        $file = \Curl::to('https://www.mangaupdates.com/series.html')->withData([

            'id' => $mu_id
        ])->get();

        $description = [];
        $description_result = preg_match_all('/<div class=(\"|\')sCat(\"|\')><b>Description<\/b><\/div>\s<div class=(\"|\')sContent(\"|\').+?\">(.+?)\s<\/div>/', $file, $description);
        if ($description_result == 0 || $description_result === false)
            return null;

        // url decode the descriptions
        array_walk($description[5], function (&$desc, $key) {

        	$desc = IntlString::convert(urldecode($desc));
        });

        /* dd($description);

            array:6 [▼
              0 => array:1 [▼
                0 => """
                  <div class="sCat"><b>Description</b></div>\n
                  <div class="sContent" style="text-align:justify">Watanuki Kimihiro is haunted by visions of ghosts and spirits. Seemingly by chance, he encounters a mysterious witch named Y&ucirc;ko, who claims she can help. In desperation, he accepts, but realizes that he&#039;s just been tricked into working for Y&ucirc;ko in order to pay off the cost of her services. Soon he&#039;s employed in her little shop- a job which turns out to be nothing like his previous work experience!\n
                  </div>
                  """
              ]
              ...
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
              ...
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
              ...
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
        array_walk($assoc_names[2], function (&$name, $key) {

            $name = IntlString::convert(urldecode($name));
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
              ...
              2 => array:7 [
                0 => "&#1088;&#1080;&#1087;&#1083;&#1077;&#1082;&#1089;&#1086;&#1075;&#1086;&#1083;&#1080;&#1082;"
                1 => "&times;&times;&times;HOLiC"
                2 => "&times;&times;&times;HOLiC&#12539;&#31840;"
                3 => "Holic"
                4 => "XXX &#12507;&#12522;&#12483;&#12463;"
                5 => "xxxHolic R&#333;"
                6 => "xxxHolic Rou"
              ]
              ...
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
              ...
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
              ...
            ]

         */

        $authors = [];
        $authors_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $authors_content[0][0], $authors);
        if ($authors_result == 0 || $authors_result === false)
            return null;

        // url decode the authors' name
        array_walk($authors[2], function (&$author, $key) {

        	$author = IntlString::convert(urldecode($author));
        });

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
              ...
            ]

         */

        $artists = [];
        $artists_result = preg_match_all('/\?id=(\d+).+?<u>(.+?)<\/u>/', $artists_content[0][0], $artists);
        if ($artists_result == 0 || $artists_result === false)
            return null;

        // url decode the artists' name
        array_walk($artists[2], function (&$artist, $key) {

        	$artist = IntlString::convert(urldecode($artist));
        });

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
			  ...
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
}