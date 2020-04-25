<?php

namespace App;

use App\Archive;
use App\ImageArchive;
use App\Manga;

use \Carbon\Carbon;
use Carbon\CarbonInterval;

class Image extends StreamableStorageFile
{
    /** @var int $mangaId */
    private $mangaId;
    /** @var string $mangaPath */
    private $mangaPath;
    /** @var int $archiveId */
    private $archiveId;
    /** @var string $archiveName */
    private $archiveName;
    /** @var int $page */
    private $page;

    public function __construct(
        Manga $manga,
        Archive $archive,
        int $page
    ){
        $this->mangaId = $manga->id;
        $this->mangaPath = $manga->path;
        $this->archiveId = $archive->id;
        $this->archiveName = $archive->name;
        $this->page = $page;
        $root = storage_path('app');
        $relativePath = "public/images/{$manga->id}/{$archive->id}/{$page}";

        parent::__construct($root, $relativePath, CarbonInterval::month());
    }

    /**
     *  Makes a cover from an image buffer.
     *  Specifying a value for width or image and null for the other will
     *  result in a proportionally resized image.
     *
     *  @param string $contents The image buffer contents.
     *  @param int $width The desired width.
     *  @param int $height The desired height.
     *  @return \Intervention\Image\Image|FALSE
     *
     */
    public static function make($contents, $width, $height)
    {
        if ($width == null || $height == null)
            $image = \Image::make($contents)->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });
        else
            $image = \Image::make($contents)->resize($width, $height);

        return $image;
    }

    /**
     * Gets the size, in bytes, of the images disk.
     *
     * @return int
     */
    public static function size()
    {
        $size = 0;
        $images = \Storage::disk('images');
        $files = $images->allFiles();

        foreach ($files as $file) {
            $size += $images->size($file);
        }

        return $size;
    }

    public function extract()
    {
        $path = \Storage::disk('images')->path("{$this->mangaId}/{$this->archiveId}");
        $imagePath = $path . DIRECTORY_SEPARATOR . strval($this->page);
        if (\File::exists($imagePath)) {
            \Log::debug("Image exists: {$imagePath}");

            return true;
        }

        \Log::debug("Extracting image: {$imagePath}");
        if (! \File::exists($path)) {
            \Log::debug("Creating directory: {$path}");

            $result = \File::makeDirectory($path, 0755, true);
            \Log::debug("Create result: {$result}");
        }

        $imgArchive = ImageArchive::open($this->mangaPath . DIRECTORY_SEPARATOR . $this->archiveName);
        if ($imgArchive->good()) {
            return $imgArchive->extract($this->page - 1, $path, strval($this->page));
        }

        return false;
    }
}
