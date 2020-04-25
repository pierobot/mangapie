<?php

namespace App;

use \Carbon\Carbon;

final class Preview extends StreamableStorageFile
{
    /** @var int $mangaId */
    private $mangaId;
    /** @var int $archiveId */
    private $archiveId;
    /** @var int $page */
    private $page;
    /** @var bool $small */
    private $small;

    /**
     * Preview constructor.
     *
     * @param Manga $manga
     * @param Archive|null $archive
     * @param int $page
     * @param bool $small
     */
    public function __construct(
        Manga $manga,
        Archive $archive,
        int $page = 1,
        bool $small = true
    ){
        $this->mangaId = $manga->id;
        $this->archiveId = $archive->id;
        $this->page = $page;
        $this->small = $small;
        $root = storage_path('app');
        $size = $small ? 'small' : 'medium';
        $relativePath = "public/previews/{$size}/{$manga->id}/{$archive->id}/{$page}";

        $this->EXPIRES_IN_DAYS = 7;

        parent::__construct(
            $root,
            $relativePath
        );
    }

    /**
     * Gets the size, in bytes, of the previews disk.
     *
     * @return int
     */
    public static function size()
    {
        $size = 0;
        $previews = \Storage::disk('previews');
        $files = $previews->allFiles();

        foreach ($files as $file) {
            $size += $previews->size($file);
        }

        return $size;
    }

    /**
     * Deletes all the covers in the disk.
     *
     * @return bool
     */
    public static function delete()
    {
        $disk = \Storage::disk('previews');
        $directories = array_merge(
            $disk->directories('small'),
            $disk->directories('medium')
        );

        $total = 0;
        /** @var string $directory */
        foreach ($directories as $directory) {
            if ($disk->deleteDirectory($directory)) {
                ++$total;
            }
        }

        return $total === count($directories);
    }

    /**
     * Determine if a preview exists for an archive.
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->root . DIRECTORY_SEPARATOR . $this->relativeFilePath);
    }

    /**
     * Saves the contents of an image as the cover.
     *
     * @param string $contents
     * @return bool
     */
    public function put(string $contents)
    {
        /** @var \Intervention\Image\Image $image */
        $image = Image::make($contents, null, ($this->small ? 250 : 500));

        /*
         * The cover is being replaced by the put operation so update the relative file path
         * in case there was no cover in the first place as it would have defaulted to the default.jpeg.
         */
        $size = $this->small ? 'small' : 'medium';
        $this->relativeFilePath = "previews/{$size}/{$this->mangaId}/{$this->archiveId}/{$this->page}";

        return \Storage::disk('previews')->put(
            "{$size}/{$this->mangaId}/{$this->archiveId}/{$this->page}",
            $image->stream('jpg')->detach());
    }
}
