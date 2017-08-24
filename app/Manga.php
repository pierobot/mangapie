<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use \Symfony\Component\Finder\Finder;

use \App\ArtistReference;
use \App\AuthorReference;
use \App\GenreInformation;
use \App\MangaInformation;

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

        return null;
    }

    private function getArchiveType($archive_name) {
        $types = [
            'zip' => 'zip',
            'cbz' => 'zip',
            'rar' => 'rar',
            'cbr' => 'rar'
        ];

        $extension = $this->getExtension($archive_name);
        if ($extension == null)
            return null;

        return array_key_exists($extension, $types) == true ? $types[$extension] : null;
    }

    private function zipEntryIsImage($stat) {
        if ($stat === false)
            return false;

        $name = $stat['name'];
        // ignore any directory entries
        if (mb_strrpos($name, '/') == mb_strlen($name))
            return false;

        $name = $stat['name'];
        // declare common image extensions
        $image_extensions = [
            'jpg',
            'jpeg',
            'png',
            'gif' // really rare but I've come across this.
        ];

        $extension = $this->getExtension($name);
        if ($extension === false)
            return false;
        
        return in_array($extension, $image_extensions) == true;
    }

    private function rarEntryIsImage($entry) {
        if ($entry == null)
            return false;

        $name = null;
        if (is_string($entry) == true)
            $name = $entry;
        else
            $name = $entry->getName();

        $image_extensions = [
            'jpg',
            'jpeg',
            'png',
            'gif' // really rare but I've come across this.
        ];

        $extension = $this->getExtension($name);
        if ($extension === false)
            return false;
        
        return in_array($extension, $image_extensions) == true;
    }

    private function zipGetImageCount($archive_path) {
        $zip = new \ZipArchive;

        if ($zip->open($archive_path) !== true)
            return false;

        $image_count = 0;
        // enumerate through the entries and filter out any based on the callback
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat !== false && $this->zipEntryIsImage($stat) == true)
                $image_count++;
        }

        $zip->close();

        return $image_count;
    }

    private function rarGetImageCount($archive_path) {
        $rar = \RarArchive::open($archive_path);
        if ($rar == false)
            return false;

        $image_count = 0;
        $entries = $rar->getEntries();
        foreach ($entries as $entry) {
            if ($this->rarEntryIsImage($entry) == true)
                $image_count++;
        }

        $rar->close();

        return $image_count;
    }

    public function getImageCount($archive_path) {
        $type = $this->getArchiveType($archive_path);

        if ($type == 'zip')
            return $this->zipGetImageCount($archive_path);

        if ($type == 'rar')
            return $this->rarGetImageCount($archive_path);

        return false;
    }

    private function getNumberTokens($name) {
        if (mb_ereg_search_init($name, "\\d+") === false)
            return null;

        $tokens = [];

        if (mb_ereg_search() === false)
            return null;

        // get first token
        $result = mb_ereg_search_getregs();
        while ($result !== false) {
            array_push($tokens, intval($result[0]));
            // get next token
            $result = mb_ereg_search_regs();
        }      

        return empty($tokens) != true ? $tokens : null;
    }

    private function zipGetEntrySize($archive_path, $index) {
        $size = null;
        $zip = new \ZipArchive;
        if ($zip->open($archive_path) !== true)
            return null;

        $indices = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat !== false && $this->zipEntryIsImage($stat) == true)
                array_push($indices, $i);
        }

        // sort the entries
        usort($indices, function ($left, $right) use($zip) {
        // all the entries should be good, as verified in above loop
            $left_stat = $zip->statIndex($left);
            $right_stat = $zip->statIndex($right);

            $left_tokens = $this->getNumberTokens($left_stat['name']);
            $right_tokens = $this->getNumberTokens($right_stat['name']);
            if ($left_tokens == null || $right_tokens == null)
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

        $stat = $zip->statIndex($indices[$index]);
        if ($stat !== false) {
            $size = $stat['size'];
        }

        $zip->close();

        return $size;
    }

    private function rarGetEntrySize($archive_path, $index) {
        $size = null;
        $rar = \RarArchive::open($archive_path);
        if ($rar == false)
            return null;

        $entries = $rar->getEntries();
        $indices = [];

        foreach ($entries as $entry) {
            $name = $entry->getName();
            if ($this->rarEntryIsImage($name) == true)
                array_push($indices, $name);
        }

        usort($indices, function ($left_name, $right_name) use($rar) {
        // all the entries should be good, as verified in above loop
            $left_tokens = $this->getNumberTokens($left_name);
            $right_tokens = $this->getNumberTokens($right_name);
            if ($left_tokens == null || $right_tokens == null)
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

        if (count($indices) > 0) {
            $entry = $rar->getEntry($indices[$index]);
            $size = $entry->getUnpackedSize();
        }

        return $size;
    }

    private function getEntrySize($archive_path, $index) {
        $type = $this->getArchiveType($archive_path);

        if ($type == 'zip')
            return $this->zipGetEntrySize($archive_path, $index);
        if ($type == 'rar')
            return $this->rarGetEntrySize($archive_path, $index);

        return null;
    }

    private function zipGetEntryData($archive_path, $archive_name, $index) {
        $data = null;
        $zip = new \ZipArchive;
        if ($zip->open($archive_path) !== true)
            return null;

        $indices = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat !== false && $this->zipEntryIsImage($stat) == true)
                array_push($indices, $i);
        }

        // sort the entries
        usort($indices, function ($left, $right) use($zip) {
        // all the entries should be good, as verified in above loop
            $left_stat = $zip->statIndex($left);
            $right_stat = $zip->statIndex($right);

            $left_tokens = $this->getNumberTokens($left_stat['name']);
            $right_tokens = $this->getNumberTokens($right_stat['name']);
            if ($left_tokens == null || $right_tokens == null)
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

        if (count($indices) > 0) {
            $stat = $zip->statIndex($indices[$index]);
            if ($stat !== false) {
                $data = $zip->getFromIndex($indices[$index]);
            }
        }

        $zip->close();

        return $data;
    }

    private function rarGetEntryData($archive_path, $archive_name, $index) {
        $data = null;

        $rar = \RarArchive::open($archive_path);
        if ($rar == false)
            return null;

        $entries = $rar->getEntries();
        $indices = [];

        foreach ($entries as $entry) {
            $name = $entry->getName();
            if ($this->rarEntryIsImage($name) == true)
                array_push($indices, $name);
        }

        // sort the entries
        usort($indices, function ($left_name, $right_name) use($rar) {
        // all the entries should be good, as verified in above loop
            $left_tokens = $this->getNumberTokens($left_name);
            $right_tokens = $this->getNumberTokens($right_name);
            if ($left_tokens == null || $right_tokens == null)
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

        if (count($indices) > 0) {
            $entry = $rar->getEntry($indices[$index]);
            $stream = $entry->getStream();
            $data = stream_get_contents($stream);
        }

        $rar->close();

        return $data !== false ? $data : null;
    }
    
    private function getEntryData($archive_path, $archive_name, $index) {
        $type = $this->getArchiveType($archive_path);

        if ($type == 'zip')
            return $this->zipGetEntryData($archive_path, $archive_name, $index);
        if ($type == 'rar')
            return $this->rarGetEntryData($archive_path, $archive_name, $index);

        return null;
    }

    private function zipGetEntryName($archive_path, $index) {
        $name = null;
        $zip = new \ZipArchive;
        if ($zip->open($archive_path) !== true)
            return null;

        $indices = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($this->zipEntryIsImage($stat) == true)
                array_push($indices, $i);
        }

        if (count($indices) > 0) {
            $stat = $zip->statIndex($indices[$index]);
            if ($stat !== false) {
                $name = $stat['name'];
            }
        }

        $zip->close();

        return $name;
    }

    private function rarGetEntryName($archive_path, $index) {
        $name = null;
        $rar = \RarArchive::open($archive_path);
        if ($rar == false)
            return null;

        $entries = $rar->getEntries();
        $indices = [];

        foreach ($entries as $entry) {
            $name = $entry->getName();
            if ($this->rarEntryIsImage($name) == true)
                array_push($indices, $name);
        }

        if (count($indices) > 0) {
            $name = $indices[$index];
        }

        return $name;
    }

    private function getEntryName($archive_path, $index) {
        $type = $this->getArchiveType($archive_path);

        if ($type == 'zip')
            return $this->zipgetEntryName($archive_path, $index);
        elseif ($type == 'rar')
            return $this->rargetEntryName($archive_path, $index);

        return null;
    }

    private function getMIME($image_name) {
        $image_extensions = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif' // really rare but I've come across this.
        ];

        $extension = $this->getExtension($image_name);
        if ($extension === null)
            return null;

        return array_key_exists($extension, $image_extensions) == true ? $image_extensions[$extension] : null;
    }

    public function getImage($archive_name, $page) {
        if ($page < 1)
            return null;

        $archive_path = $this->getPath() . '/' . $archive_name;
        
        $name = $this->getEntryName($archive_path, $page - 1);
        if ($name == null)
            return null;

        $data = $this->getEntryData($archive_path, $archive_name, $page - 1);
        if ($data == null)
            return null;

        $size = $this->getEntrySize($archive_path, $page - 1);
        if ($size == null)
            return null;

        $mime = $this->getMIME($name);
        if ($mime === null)
            return null;

        return [
            'data' => $data,
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
            $archive['name'] = $file->getFileName();

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
        $artist_references = ArtistReference::where('manga_id', '=', $id)->get();
        if ($artist_references != null) {

            foreach ($artist_references as $reference) {

                $reference->forceDelete();
            }
        }

        $author_references = AuthorReference::where('manga_id', '=', $id)->get();
        if ($author_references != null) {

            foreach ($author_references as $reference) {

                $reference->forceDelete();
            }
        }

        $genre_information = GenreInformation::where('manga_id', '=', $id)->get();
        if ($genre_information != null) {

            foreach ($genre_information as $information) {

                $information->forceDelete();
            }
        }

        $manga_information = MangaInformation::where('id', '=', $id)->get();
        if ($manga_information != null) {

            foreach ($manga_information as $information) {

                $manga_information->forceDelete();
            }
        }

        parent::delete();
    }
}
