<?php

namespace App\Jobs;

use App\Manga;
use App\User;
use Carbon\Carbon;

use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IncrementMangaViews implements ShouldQueue
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
     * @return void
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
        $timeEnabled = \Cache::tags(['config', 'views', 'time'])->get('enabled', true);
        $timeThreshold = CarbonInterval::fromString(\Cache::tags(['config', 'views', 'time'])->get('threshold', '3h'));

        $views = $this->user->mangaViews();
        $shouldIncrement = true;

        if ($timeEnabled) {
            // only increment if the last view was >= the time threshold
            $lastView = $views->where('manga_id', $this->manga->id)
                ->where('created_at', '<=', Carbon::now()->sub($timeThreshold))
                ->orderByDesc('created_at')
                ->first();

            if (empty($lastView))
                $shouldIncrement = false;
        }

        if ($shouldIncrement) {
            $views->create([
                'manga_id' => $this->manga->id
            ]);
        }
    }
}
