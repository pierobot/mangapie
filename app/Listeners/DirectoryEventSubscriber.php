<?php

namespace App\Listeners;

use \Symfony\Component\Finder\Finder;

use App\Archive;
use App\Library;
use App\Manga;

use App\Events\NewDirectoryEvent;
use App\Events\RemovedDirectoryEvent;

class DirectoryEventSubscriber
{
    private static function isLastPathSegment($childName, $parentPath, $rootPath)
    {
        $result = false;

        // find the last directory separator
        $nameStartPos = mb_strrpos($parentPath, DIRECTORY_SEPARATOR);
        if ($nameStartPos !== false) {
            $result = mb_strlen($rootPath) == $nameStartPos;
        }

        return $result;
    }

    private static function nextPathSegment($path, $rootPath)
    {
        $result = false;

        $rootStartPos = mb_strpos($path, $rootPath);
        if ($rootStartPos !== false) {
            $rootEndPos = $rootStartPos + mb_strlen($rootPath) + 1;

            // find the last directory separator
            $nameEndPos = mb_strpos($path, DIRECTORY_SEPARATOR, $rootEndPos);

            $result = ($nameEndPos === false ? mb_substr($path, $rootEndPos) :
                                               mb_substr($path, $rootEndPos, $nameEndPos - $rootEndPos));
        }

        return $result;
    }

    private static function relativePath($path, $rootPath)
    {
        $result = false;

        $rootStartPos = mb_strpos($path, $rootPath);
        if ($rootStartPos !== false) {
            $rootEndPos = $rootStartPos + mb_strlen($rootPath) + 1;

            $result = mb_substr($path, $rootEndPos);
        }

        return $result;
    }

    private function onAddManga($name, $path, $rootPath)
    {
        $library = Library::where('path', $rootPath)->first();

        if ($library !== null) {
            $manga = Manga::create([
                'name' => $name,
                'path' => $path,
                'library_id' => $library->getId()
            ]);

            echo " " . __METHOD__ . " :: " . $manga->getName() . "\n";
        }
    }

    private function onAddDirectory($mangaName, $name, $path, $rootPath, $isSymbolicLink)
    {
        $manga = Manga::where('name', $name)->first();

        /**
         * There are two situations, that I've encountered, where execution reaches this stage.
         *
         * The first, is the creation of a regular directory; and the second, is the creation
         * of a symbolic link that points to a directory.
         *
         * In the case of the first, we do not need to scan the directory for archives
         * as they will receive their own events.
         *
         * If it is the second, then we will not receive any other events, which means we must scan the directory.
         */
        if ($manga !== null && $isSymbolicLink) {

            echo " " . __METHOD__ . " :: " . $name . "\n";

            $archives = Finder::create()->in($rootPath . DIRECTORY_SEPARATOR . $name)
                ->followLinks()
                ->name('/\.zip$/i')
                ->name('/\.cbz$/i')
                ->name('/\.rar$/i')
                ->name('/\.cbr$/i');

            $archives->sort(function ($left, $right) {
                return strnatcasecmp($left->getRelativePathName(), $right->getRelativePathName());
            });

            foreach ($archives as $archive) {
                Archive::updateOrCreate([
                    'manga_id' => $manga->getId(),
                    'name' => $archive->getRelativePathName(),
                    'size' => \File::size($archive->getPathName())
                ]);
            }
        }
    }

    public function onAdd(NewDirectoryEvent $event)
    {
        $isInRootOfLibrary = self::isLastPathSegment($event->name, $event->path, $event->rootPath);

        /**
         * if the directory was created in the root of the library path then
         * we need to treat it as a manga. If not, it's just a directory inside a manga.
         */
        if ($isInRootOfLibrary) {
            $this->onAddManga($event->name, $event->path, $event->rootPath);
        } else {
            $name = self::nextPathSegment($event->path, $event->rootPath);

            $this->onAddDirectory($name, $event->name, $event->path, $event->rootPath, $event->isSymbolicLink);
        }
    }

    private function onRemoveManga($name, $path, $rootPath)
    {
        $manga = Manga::where('path', $path)->first();
        if ($manga !== null) {
            echo " " . __METHOD__ . " :: " . $manga->getName() . "\n";

            $manga->forceDelete();
        }
    }

    private function onRemoveDirectory($mangaName, $name, $path, $rootPath, $isSymbolicLink)
    {
        $manga = Manga::where('name', $mangaName)->first();

        if ($manga !== null && $isSymbolicLink) {
            /**
             * There are two situations, that I've encountered, where execution reaches this stage.
             *
             * The first, is the removal of a regular directory; and the second, is the removal
             * of a symbolic link that points to a directory.
             *
             * In the case of the first, we do not need to scan the directory for archives
             * as they will receive their own events.
             *
             * If it is the second, then we will not receive any other events, which means we must scan
             * existing archives in the database for matching paths and remove them.
             */

            echo " " . __METHOD__ . " :: " . $manga->getName() . "\n";

            // filter out the archives whose relative name matches that of the removed directory
            $archives = $manga->archives;
            $archives = $archives->filter(function ($archive) use ($manga, $path) {
                $relativePath = self::relativePath($path, $manga->getPath());

                return mb_strpos($archive->getName(), $relativePath) !== false;
            });

            foreach ($archives as $archive) {
                $archive->forceDelete();
            }
        }
    }

    public function onRemove(RemovedDirectoryEvent $event)
    {
        $isInRootOfLibrary = self::isLastPathSegment($event->name, $event->path, $event->rootPath);

        /**
         * if the directory was created in the root of the library path then
         * we need to treat it as a manga. If not, it's just a directory inside a manga.
         */

        // if the paths are equal then a directory was created in the root of a library
        if ($isInRootOfLibrary) {
            $this->onRemoveManga($event->name, $event->path, $event->rootPath);
        } else {
            $name = self::nextPathSegment($event->path, $event->rootPath);

            $this->onRemoveDirectory($name, $event->name, $event->path, $event->rootPath, $event->isSymbolicLink);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\NewDirectoryEvent::class,
            'App\Listeners\DirectoryEventSubscriber@onAdd'
        );

        $events->listen(
            \App\Events\RemovedDirectoryEvent::class,
            'App\Listeners\DirectoryEventSubscriber@onRemove'
        );
    }
}
