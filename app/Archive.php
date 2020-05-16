<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
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
     *  @return Archive
     */
    private function getAdjacentArchive(bool $next = true)
    {
        /** @var Collection $archives */
        // Sort them because there's no guarantee on their IDs being in order since transfers can be out of order
        $archives = $this->manga->archives()->orderBy('name', 'asc')->get();
        // Determine if the current archive is in the root directory or a sub directory
        $fileInfo = new \SplFileInfo($this->name);
        $basePath = $fileInfo->getPath();
        $isInRoot = empty($basePath);
        $currentName = $this->name;

        // Filter out the archives that are not in the same working directory
        if ($isInRoot) {
            $archives = $archives->filter(function (Archive $archive) {
                $fileInfo = new \SplFileInfo($archive->name);
                $path = $fileInfo->getPath();

                return empty($path);
            });
        } else {
            $archives = $archives->filter(function (Archive $archive) use ($basePath) {
                $fileInfo = new \SplFileInfo($archive->name);
                $path = $fileInfo->getPath();

                return $basePath == $path;
            });
        }

        if ($next) {
            return $archives->first(function (Archive $archive) use ($currentName) {
                return IntlString::strcmp($archive->name, $currentName) > 0;
            });
        } else {
            return $archives->last(function (Archive $archive) use ($currentName) {
                return IntlString::strcmp($archive->name, $currentName) < 0;
            });
        }
    }

    /**
     * Gets the previous archive in relation to $this.
     *
     * @return Archive
     */
    public function getPreviousArchive()
    {
        return $this->getAdjacentArchive(false);
    }

    /**
     * Gets the next archive in relation to $this.
     *
     * @return Archive
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
