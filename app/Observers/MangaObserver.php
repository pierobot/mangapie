<?php

namespace App\Observers;

use \Symfony\Component\Finder\Finder;

use App\Archive;
use App\Manga;
use App\Sources\MangaUpdates;

class MangaObserver
{
    public static function getArchives($path)
    {
        try {
            // get all the files in the path and filter by archives
            $files = Finder::create()->in($path)
                ->name('/\.zip$/i')
                ->name('/\.cbz$/i')
                ->name('/\.rar$/i')
                ->name('/\.cbr$/i');
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        // sort by number tokens
        $files->sort(function ($left, $right) {
            return strnatcasecmp($left->getFilename(), $right->getFilename());
        });

        $archives = [];
        foreach ($files as $file) {
            $archive = [];
            $archive['name'] = $file->getRelativePathname();
            $archive['size'] = $file->getSize();

            array_push($archives, $archive);
        }

        return $archives;
    }

    public function created(Manga $manga)
    {
        // don't autofill if running within a test
        if (\Config::get('app.env') !== 'testing')
            MangaUpdates::autofill($manga);

        $path = $manga->path;
        $archives = self::getArchives($path);
        if (empty($archives))
            return;

        foreach ($archives as $archive) {
            $archivePath = $path . DIRECTORY_SEPARATOR . $archive['name'];
            $handle = fopen($archivePath, 'rb');
            if (! $handle)
                continue;

            /**
             * Attempt to acquire an exclusive lock on the file.
             *
             * If the archive is incomplete because it's currently being written to,
             * then we should not be able to acquire an exclusive lock.
             *
             * If the operation would block or returns false, then that means
             * it's probably safe to assume it's incomplete - so ignore it.
             * When it becomes complete, we will get the IN_CLOSE_WRITE event.
             */
            $wouldBlock = false;
            $locked = flock($handle, LOCK_EX | LOCK_NB, $wouldBlock);
            if ($locked && ! $wouldBlock) {
                flock($handle, LOCK_UN);

                Archive::updateOrCreate([
                    'manga_id' => $manga->id,
                    'name' => $archive['name'],
                    'size' => $archive['size']
                ]);
            }

            fclose($handle);
        }
    }
}