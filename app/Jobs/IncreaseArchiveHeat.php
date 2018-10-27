<?php

namespace App\Jobs;

use App\Archive;
use App\Heat;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IncreaseArchiveHeat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Archive
     */
    private $archive;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Archive $archive
     */
    public function __construct(User $user, Archive $archive)
    {
        $this->user = $user;
        $this->archive = $archive;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timeEnabled = \Cache::tags(['config', 'views', 'time'])->get('enabled', true);
        $timeThreshold = CarbonInterval::fromString(\Cache::tags(['config', 'views', 'time'])->get('threshold', '3h'));
        $lastView = $this->user->archiveViews->sortByDesc('created_at')->first();
        $needsUpdate = true;

        $heat = new Heat($this->archive);

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
