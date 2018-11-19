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

        $path = $manga->getPath();
        $archives = self::getArchives($path);
        if (empty($archives))
            return;

        foreach ($archives as $archive) {
            /**
             * If the file is not writable then we can ignore it because we will
             * either receive a watch event for it or it will be manually added with a library scan.
             */
            if (\File::isWritable($path) === false)
                continue;

            Archive::updateOrCreate([
                'manga_id' => $manga->getId(),
                'name' => $archive['name'],
                'size' => $archive['size']
            ]);

            // there is no need to fire a new archive event because no one will be 'watching'
        }
    }
}