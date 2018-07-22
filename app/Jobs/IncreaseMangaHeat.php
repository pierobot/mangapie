<?php

namespace App\Jobs;

use App\Heat;
use App\Manga;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IncreaseMangaHeat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Manga
     */
    private $manga;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Manga $manga
     */
    public function __construct(User $user, Manga $manga)
    {
        $this->user = $user;
        $this->manga = $manga;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timeEnabled = \Config::get('app.views.time.enabled');
        $timeThreshold = CarbonInterval::fromString(\Config::get('app.views.time.threshold'));
        $lastView = $this->user->mangaViews->sortByDesc('created_at')->first();
        $needsUpdate = true;

        $heat = new Heat($this->manga);

        if (! empty($lastView) && $timeEnabled === true) {
            $createdAt = Carbon::createfromTimeString($lastView->created_at);
            $timeElapsed = Carbon::now()->diffAsCarbonInterval($createdAt);

            if ($timeElapsed->compare($timeThreshold) < 0)
                $needsUpdate = false;
        }

        if ($needsUpdate) {
            $heat->update();
        }
    }
}
