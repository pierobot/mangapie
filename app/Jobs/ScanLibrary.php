<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imtigger\LaravelJobStatus\Trackable;

use App\Library;
use App\Manga;
use App\Scanner;

class ScanLibrary implements ShouldQueue
{
    use InteractsWithQueue, Dispatchable, Queueable, SerializesModels, Trackable;

    private $libraryId;

    /**
     * Create a new job instance.
     *
     * @param \App\Library $library The library to scan.
     *
     * @return void
     */
    public function __construct(Library $library)
    {
        $this->libraryId = $library->id;

        $this->prepareStatus();
        $this->setInput(['library_id' => $this->libraryId]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $library = Library::findOrFail($this->libraryId)->load([ 'manga', 'manga.archives' ]);
        $paths = \File::directories($library->getPath());

        $this->setProgressMax(count($paths));

        // scan and add new directories
        foreach ($paths as $path) {
            $manga = Manga::updateOrCreate([
                'name' => Scanner::clean(pathinfo($path, PATHINFO_BASENAME)),
                'path' => $path,
                'library_id' => $library->getId()
            ]);

            // scan for new archives
            $archives = Scanner::getArchives($manga->getId(), $path);
            if (empty($archives))
                continue;

            $names = [];
            foreach ($archives as $archive) {
                $names[] = $archive['name'];
            }

            $allArchives = $manga->archives;
            // filter out the ones that are not present in the database
            $newArchives = collect($archives)->reject(function ($archive) use ($allArchives) {
                return $allArchives->where('name', $archive['name'])->first() != null;
            });

            // filter out the ones that still exist
            $removedArchives = $allArchives->reject(function ($archive) use ($names) {
                return in_array($archive['name'], $names);
            });

            if ($removedArchives->count() > 0) {
                \Event::fire(new \App\Events\Archive\RemovedArchives($removedArchives));
            }

            if ($newArchives->count() > 0) {
                \Event::fire(new \App\Events\Archive\NewArchives($newArchives));
            }

            $this->incrementProgress();
        }
    }
}
