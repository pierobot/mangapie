<?php

namespace App\Jobs;

use App\Archive;
use App\Heat;
use App\Image;
use App\Manga;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Finder\Finder;

class CleanupImageDisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var float
     */
    private $threshold ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->threshold = \Config::get('app.heat.threshold');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dirs = Finder::create()
            ->in(Image::rootPath())
            ->directories()
            ->depth('1');

        foreach ($dirs as $dir) {
            $mangaId = self::getMangaId($dir);
            $archiveId = self::getArchiveId($dir);

            $manga = Manga::find($mangaId);
            $archive = Archive::find($archiveId);

            /*
             * Do not delete the directory if the manga or archive is missing.
             * Rebuilding an image cache with a large amount of manga and archives could be expensive.
             *
             * Instead, just add a warning to the log file.
             */
            if (empty($manga) || empty($archive)) {
                \Log::warning('Found an image directory with no database record. Consider deleting it.', [
                    'path' => $dir->getPath()
                ]);

                continue;
            }

            // only clean if the heat is below the threshold
            $heat = Heat::get($archive);
            if (! empty($heat) && $heat < $this->threshold) {
                $deletedSuccessfully = Image::disk()->deleteDirectory(Image::relativePath($manga, $archive));

                if (! $deletedSuccessfully)
                    \Log::error('Unable to delete image directory.', [
                        'path' => $dir->getPath()
                    ]);
            }
        }
    }

    /**
     * Gets the manga id from a directory SplFileInfo object.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $dir
     * @return int|false
     */
    public static function getMangaId(\Symfony\Component\Finder\SplFileInfo $dir)
    {
        return ! empty($dir) ? strval($dir->getRelativePath()) : false;
    }

    /**
     * Gets the archive id from a directory SplFileInfo object.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $dir
     * @return int|false
     */
    public static function getArchiveId(\Symfony\Component\Finder\SplFileInfo $dir)
    {
        return ! empty($dir) ? strval($dir->getFilename()) : false;
    }
}
