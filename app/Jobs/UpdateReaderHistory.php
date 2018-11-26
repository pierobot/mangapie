<?php

namespace App\Jobs;

use App\Archive;
use App\Manga;
use App\ReaderHistory;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateReaderHistory implements ShouldQueue
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
     * @var Archive
     */
    private $archive;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $pageCount;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Manga $manga
     * @param Archive $archive
     * @param int page
     * @return void
     */
    public function __construct(User $user, Manga $manga, Archive $archive, int $page, int $pageCount)
    {
        $this->user = $user;
        $this->manga = $manga;
        $this->archive = $archive;
        $this->page = $page;
        $this->pageCount = $pageCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ReaderHistory::updateOrCreate([
            'user_id' => $this->user->id,
            'manga_id' => $this->manga->id,
            'archive_name' => $this->archive->name,
            'page_count' => $this->pageCount,
        ], [
            'user_id' => $this->user->id,
            'manga_id' => $this->manga->id,
            'archive_name' => $this->archive->name,
            'page' => $this->page,
            'page_count' => $this->pageCount,
        ]);
    }
}
