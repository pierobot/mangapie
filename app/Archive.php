<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    public $fillable = ['manga_id', 'name', 'size'];

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public static function convertSizeToReadable($bytes)
    {
        $sizes = [ 'B', 'KB', 'MB', 'GB' ];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return number_format(round($bytes, 2), 2) . ' ' . $sizes[$i];
    }

    public function getSize($asReadable = true)
    {
        return $asReadable == true ? self::convertSizeToReadable($this->size) : $this->size;
    }

    public function getPageCount()
    {
        $archive = ImageArchive::open($this->manga->path . DIRECTORY_SEPARATOR . $this->name);
        if (! $archive)
            return false;

        $images = $archive->getImages();
        if (! $images)
            return false;

        return count($images);
    }

    /**
     *  Gets the adjacent archive in relation to $this.
     *
     *  @param bool $next Boolean value that indicates to get the next or previous archive.
     *  @return Archive|false
     */
    private function getAdjacentArchive($next = true)
    {
        $archives = $this->manga->getArchives();
        if (empty($archives))
            return false;

        // find the index of $name in $archives
        for ($i = 0, $max = count($archives) - 1; $i < $max; $i++) {
            // if the names match then we can get the next archive
            if ($archives[$i]->name == $this->name) {
                if (! $next) {
                    // previous archive wanted
                    // check if we were given the first archive
                    if ($i == 0)
                        break;

                    return $archives[$i - 1];
                } else {
                    // next archive wanted
                    return $archives[$i + 1];
                }
            }
        }

        return false;
    }

    /**
     * Gets the previous archive in relation to $this.
     *
     * @return Archive|false
     */
    public function getPreviousArchive()
    {
        return $this->getAdjacentArchive(false);
    }

    /**
     * Gets the next archive in relation to $this.
     *
     * @return Archive|false
     */
    public function getNextArchive()
    {
        return $this->getAdjacentArchive();
    }

    public function getPreloadUrls($startPage, $count = 4)
    {
        $imgArchive = ImageArchive::open($this->manga->path . DIRECTORY_SEPARATOR . $this->name);
        $pageCount = count($imgArchive->getImages());
        if (! $imgArchive || ! $count || $startPage == $pageCount)
            return false;

        // ensure we only build up to $count or less. +1 because we don't count the starting page
        $difference = $pageCount - $startPage;
        if ($difference >= $count) {
            $count = 4;
        } else {
            $count = $difference;
        }

        $urls = [];
        ++$startPage;
        for ($i = $startPage; $i < $startPage + $count; $i++) {
            $urls[] = url()->action('ReaderController@image', [$this->manga->getId(), $this->id, $i]);
        }

        return $urls;
    }
}
