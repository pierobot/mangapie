<?php

namespace App\Jobs;

use App\Manga;
use App\User;
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
        $this->user->mangaViews()->create([
            'manga_id' => $this->manga->id
        ]);
    }
}
