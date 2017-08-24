<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \QueryPath;
use \App\Author;
use \App\AuthorReference;
use \App\Artist;
use \App\ArtistReference;
use \App\Genre;
use \App\GenreInformation;
use \App\Manga;

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
        // https://www.mangaupdates.com/series.html?stype=title&search=asd&page=1
        $results = null;

        /*
            <td width="32%" class="text pad col1" bgcolor=""><a href="https://www.mangaupdates.com/series.html?id=88" alt="Series Info">Berserk</a></td>
        */
        $qp = html5qp('https://www.mangaupdates.com/series.html?stype=title&search=' . urlencode($name) . '&page=' . $page);
                
        for ($i = 0; $i < $qp->size(); $i++) {
            $a_tag = $qp->find('td.col1>a')->get($i);

            // If the href attribute does not exist then skip it
            if ($a_tag->attributes['href'] == null)
                continue;

            $url = $a_tag->attributes['href']->firstChild->wholeText;
            $results[$i]['url'] = $url;
            
            if ($a_tag->firstChild->firstChild != null) {
                // <a href="https://www.mangaupdates.com/series.html?id=88" alt="Series Info"><i>Berserk Max</i></a>
                $results[$i]['name'] = $a_tag->firstChild->firstChild->wholeText;
            } else {
                // <a href="https://www.mangaupdates.com/series.html?id=88" alt="Series Info">Berserk</a>
                $results[$i]['name'] = $a_tag->firstChild->wholeText;
            }

            // get the levenshtein distance between the mangaupdates name and $name
            $results[$i]['distance'] = levenshtein($name, $results[$i]['name']); // to-do: change from levenshtein to jaro-winkler

            // find the start of the id pattern
            $id_start = strpos($url, '?id=');

            if ($id_start !== false) {
                // add 4 to skip the ?id=
                $results[$i]['mu_id'] = intval(substr($url, $id_start + 4));
            }
        }

        // sort based on levenshtein distance
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
        $information = null;

        $qp = html5qp('https://www.mangaupdates.com/series.html?id=' . $mu_id);
        
        $information['mu_id'] = $mu_id;

        $categories = $qp->find('div.sCat>b');
        $contents = $qp->find('div.sContent');
        /*
            $value should be:
                <div class='sCat'>
                    <b>Some text</b>
                </div>

            Every sCat has its own respective sContent.
            They should always be the same index.
        */
        for ($i = 0; $i < $categories->size(); $i++) {
            $categories_children = $categories->get($i);
            $content_children = $contents->get($i);
            
            if ($categories_children->firstChild == null || $content_children->firstChild == null)
                continue;

            $category = $categories->get($i)->firstChild->wholeText;

            if ($category === 'Description') {
                $information['description'] = $content_children->firstChild->wholeText;
                // The descriptions might have some html elements; handle those cases
                $sibling = $content_children->firstChild->nextSibling;
                while ($sibling != null) {
                    // We only want text
                    if ($sibling->nodeType == XML_TEXT_NODE) {
                        $information['description'] .= $sibling->wholeText;
                    }
                    // Get the next sibling
                    $sibling = $sibling->nextSibling;
                }
            }
            elseif ($category === 'Type') {
                $information['type'] = $content_children->firstChild->wholeText;
            }
            elseif ($category === 'Associated Names') {
                $assoc_names = [];
                $sibling = $content_children->firstChild;

                while ($sibling != null) {
                    /*
                        <div class="sContent">Берсерк<br>ベルセルク<br>ברזרק<br>برزرک<br>烙印勇士<br>烙印战士<br>เบอร์เซิร์ก<br>베르세르크<br>Berserk Max<br></div>
                    */
                    // don't need the br elements
                    if ($sibling->nodeType == XML_TEXT_NODE) {
                        array_push($assoc_names, $sibling->wholeText);
                    }

                    $sibling = $sibling->nextSibling;
                }

                $information['assoc_names'] = $assoc_names;
            }
            elseif ($category === 'Genre') {
                /*
                        <a rel="..." href="...">
                            <u>Genre</u>
                        </a>
                */
                $genre_names = [];
                // get the first a tag element
                $sibling = $content_children->firstChild;
                // iterate over the sContent children
                while ($sibling != null) {
                    // we don't need anything that isnt' an element node
                    if ($sibling->nodeType == XML_ELEMENT_NODE) {
                        // only looking for a tag elements
                        if ($sibling->tagName == 'a') {
                            // the a tag should have a u tag element as its only child
                            $child = $sibling->firstChild;
                            // ensure the child fits what we're looking for
                            if ($child != null &&
                                $child->nodeType == XML_ELEMENT_NODE &&
                                $child->tagName == 'u') {
                                if ($child->firstChild != null)
                                    array_push($genre_names, $child->firstChild->wholeText);
                            }
                        }
                    }

                    // Get the next sibling
                    $sibling = $sibling->nextSibling;
                }

                $information['genres'] = $genre_names;
            }
            elseif ($category === 'Author(s)') {
                // <div class="sContent"><a href="https://www.mangaupdates.com/authors.html?id=670" title="Author Info"><u>MIURA Kentaro</u></a><br></div>
                $authors = [];

                $sibling = $content_children->firstChild;
                while ($sibling != null) {
                    if ($sibling->nodeType == XML_ELEMENT_NODE && $sibling->tagName == 'a') {
                        $child = $sibling->firstChild;
                        if ($child != null) {
                            if ($child->nodeType == XML_ELEMENT_NODE && $child->tagName == 'u') {
                                if ($child->firstChild != null)
                                    array_push($authors, $child->firstChild->wholeText);
                            } else if ($child->nodeType == XML_TEXT_NODE) {
                                array_push($authors, $child->wholeText);
                            }
                        }
                    }

                    $sibling = $sibling->nextSibling;
                }

                $information['authors'] = $authors;

            }
            elseif ($category === 'Artist(s)') {
                // <div class="sContent"><a href="https://www.mangaupdates.com/authors.html?id=670" title="Author Info"><u>MIURA Kentaro</u></a><br></div>
                $artists = [];

                $sibling = $content_children->firstChild;
                while ($sibling != null) {
                    if ($sibling->nodeType == XML_ELEMENT_NODE && $sibling->tagName == 'a') {
                        $child = $sibling->firstChild;
                        if ($child != null) {
                            if ($child->nodeType == XML_ELEMENT_NODE && $child->tagName == 'u') {
                                if ($child->firstChild != null)
                                    array_push($artists, $child->firstChild->wholeText);
                            } else if ($child->nodeType == XML_TEXT_NODE) {
                                array_push($artists, $child->wholeText);
                            }
                        }
                    }

                    $sibling = $sibling->nextSibling;
                }

                $information['artists'] = $artists;
            }
            elseif ($category === 'Year') {
                $information['year'] = intval($content_children->firstChild->wholeText);
            }
                
        }

        /*
         * don't forget to use array_key_exists for the above
         */

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

        $search_results = MangaInformation::searchMangaUpdates($name);

        if (sizeof($search_results) != 0) {
            $mu_id = $search_results[0]['mu_id'];
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
