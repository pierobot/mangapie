<?php

namespace App\Jobs;

use App\Archive;
use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IncrementArchiveViews implements ShouldQueue
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
     * @return void
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
        $this->user->archiveViews()->create([
            'archive_id' => $this->archive->id
        ]);
    }
}
