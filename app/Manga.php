<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use \Symfony\Component\Finder\Finder;

use \App\ArtistReference;
use \App\AuthorReference;
use \App\GenreInformation;
use \App\MangaInformation;
use \App\ImageArchive;

class Manga extends Model
{
    //
    protected $fillable = ['name', 'path', 'library_id'];

    public function scopeSearch($query, $value) {

        return empty($value) ? $query : $query->whereRaw("match(name) against(? in boolean mode)", [$value]);
    }

    public function scopeFromLibrary($library_ids) {

        return Manga::whereIn('library_id', $library_ids);
    }

    private function getExtension($name) {

        $extension_start = mb_strrpos($name, '.');
        if ($extension_start > 0) {
            return mb_strtolower(substr($name, $extension_start + 1));
        }

        return false;
    }

    private function getNumberTokens($name) {

        if (mb_ereg_search_init($name, "\\d+") === false)
            return false;

        $tokens = [];

        if (mb_ereg_search() === false)
            return false;

        // get first token
        $result = mb_ereg_search_getregs();
        while ($result !== false) {
            array_push($tokens, intval($result[0]));
            // get next token
            $result = mb_ereg_search_regs();
        }      

        return empty($tokens) != true ? $tokens : false;
    }

    private function getMIME($image_name) {

        $image_extensions = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif' // really rare but I've come across this.
        ];

        $extension = $this->getExtension($image_name);
        if ($extension === false)
            return false;

        return array_key_exists($extension, $image_extensions) == true ? $image_extensions[$extension] : false;
    }

    public function getImage($archive_name, $page) {

        if ($page < 1)
            return false;

        $archive_path = $this->getPath() . '/' . $archive_name;

        $archive = ImageArchive::open($archive_path);
        if ($archive === false)
            return false;
       
        $images = $archive->getImages();
        usort($images, function ($left, $right) {
            // all the entries should be good, as verified in above loop
            $left_tokens = $this->getNumberTokens($left['name']);
            $right_tokens = $this->getNumberTokens($right['name']);
            if ($left_tokens === false || $right_tokens === false)
                return 0;

            $left_token_count = count($left_tokens);
            $right_token_count = count($right_tokens);
            $min_token_count = min($left_token_count, $right_token_count);

            for ($i = 0; $i < $min_token_count; $i++) {

                $left_token = $left_tokens[$i];
                $right_token = $right_tokens[$i];
                if ($left_token < $right_token)
                    return -1;
                elseif ($left_token > $right_token)
                    return 1;
            }

            // if we reach here, then all the tokens up to $min_token_count are equal
            if ($left_token_count == $right_token_count)
                return 0;

            return $left_token_count < $right_token_count ? -1 : 1;                    
        });

        $index = $images[$page - 1]['index'];
        $image = $archive->getInfo($index);
        $name = $image['name'];
        $size = 0;
        $contents = $archive->getContents($index, $size);
        $mime = $this->getMIME($name);
        if ($mime === false)
            return false;

        return [
            'contents' => $contents,
            'size' => $size,
            'mime' => $mime
        ];
    }

    public function getArchives() {

        // get all the files in the path and filter by archives
        $files = Finder::create()->in($this->path)
                                 ->name('*.zip')
                                 ->name('*.cbz')
                                 ->name('*.rar')
                                 ->name('*.cbr');

        // sort by number tokens
        $files->sort(function ($left, $right) {
            $left_tokens = $this->getNumberTokens($left->getFilename());
            $right_tokens = $this->getNumberTokens($right->getFilename());

            if ($left_tokens == null || $right_tokens === null)
                return 0;

            $left_token_count = count($left_tokens);
            $right_token_count = count($right_tokens);
            $min_token_count = min($left_token_count, $right_token_count);

            for ($i = 0; $i < $min_token_count; $i++) {
                $left_token = $left_tokens[$i];
                $right_token = $right_tokens[$i];

                if ($left_token < $right_token)
                    return -1;
                elseif ($left_token > $right_token)
                    return 1;
            }

            // if we reach here, then all the tokens up to $min_token_count are equal
            if ($left_token_count == $right_token_count)
                return 0;

            return $left_token_count < $right_token_count ? -1 : 1;
        });    

        $archives = [];
        foreach ($files as $file) {
            $archive = [];
            $archive['name'] = $file->getRelativePathname();

            array_push($archives, $archive);
        }

        return $archives != [] ? $archives : null;
    }

    public function getId() {

        return $this->id;
    }

    public function getName() {

        return $this->name;
    }

    public function getPath() {

        return $this->path;
    }

    public function getLibraryId() {

        return $this->library_id;
    }

    public function forceDelete() {

        // delete all information and references that belongs to this manga

        $id = $this->getId();

        $artist_references = ArtistReference::where('manga_id', '=', $id)->forceDelete();

        $author_references = AuthorReference::where('manga_id', '=', $id)->forceDelete();

        $genre_information = GenreInformation::where('manga_id', '=', $id)->forceDelete();

        $manga_information = MangaInformation::where('id', '=', $id)->forceDelete();

        parent::delete();
    }
}
