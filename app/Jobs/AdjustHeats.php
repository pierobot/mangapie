<?php

namespace App\Jobs;

use App\Archive;
use App\Heat;
use App\Manga;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AdjustHeats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $manga = Manga::all('id');
        $mangaIds = $manga->map(function (Manga $manga) {
            return $manga->id;
        })->toArray();

        $archives = Archive::all('id');
        $archiveIds = $archives->map(function (Archive $archive) {
            return $archive->id;
        })->toArray();

        $mangaHeats = \Cache::tags('manga_heat')->many($mangaIds);
        $archiveHeats = \Cache::tags('archive_heat')->many($archiveIds);

        foreach ($mangaHeats as $index => $heat) {
            if (! empty($heat)) {
                Heat::update($manga->find($heat->modelId), false);
            }
        }

        foreach ($archiveHeats as $index => $heat) {
            if (! empty($heat))
                Heat::update($archives->find($heat->modelId), false);
        }
    }
}
