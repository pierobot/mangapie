<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\NewArchiveEvent;
use App\Events\RemovedArchiveEvent;

use App\Archive;
use App\Manga;

class ArchiveEventSubscriber
{
    private function name($path, $rootPath)
    {
        $result = false;

        $rootStartPos = mb_strpos($path, $rootPath);
        if ($rootStartPos !== false) {

            $nameStartPos = $rootStartPos + mb_strlen($rootPath) + 1;
            $nameEndPos = mb_strpos($path, DIRECTORY_SEPARATOR, $nameStartPos);

            if ($nameEndPos !== false)
                $result = mb_substr($path, $nameStartPos, $nameEndPos - $nameStartPos);
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

    public function onAdd(NewArchiveEvent $event)
    {
        $name = $this->name($event->path, $event->rootPath);
        $manga = Manga::where('name', $name)->first();

        if ($manga !== null) {
            $relativeName = self::relativePath($event->path, $manga->getPath());
            $isArchive = preg_match('/\.(zip|rar|cb(z|r))$/mi', $relativeName);

            if ($isArchive !== false) {
                $archive = Archive::updateOrCreate([
                    'manga_id' => $manga->getId(),
                    'name' => $relativeName,
                    'size' => filesize($event->path)
                ]);

                echo " " . __METHOD__ . " Archive :: " . $archive->getName() . "\n";
            }
        }
    }

    public function onRemove(RemovedArchiveEvent $event)
    {
        $name = $this->name($event->path, $event->rootPath);
        $manga = Manga::where('name', $name)->first();

        if ($manga !== null) {
            $relativeName = self::relativePath($event->path, $manga->getPath());
            $isArchive = preg_match('/\.(zip|rar|cb(z|r))$/mi', $relativeName);

            if ($isArchive) {
                $archive = Archive::where('name', $relativeName)->first();

                if ($archive !== null) {
                    $archive->forceDelete();

                    echo " " . __METHOD__ . " Archive :: " . $archive->getName() . "\n";
                }
            }
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
            \App\Events\NewArchiveEvent::class,
            'App\Listeners\ArchiveEventSubscriber@onAdd'
        );

        $events->listen(
            \App\Events\RemovedArchiveEvent::class,
            'App\Listeners\ArchiveEventSubscriber@onRemove'
        );
    }
}
