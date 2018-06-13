<?php

namespace App\Console\Commands;

use App\Watcher;
use Illuminate\Console\Command;

use App\Library;
use App\Manga;
use App\Archive;

class Watch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mangapie:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Begins watching libraries for the addition and removal of directories and files.';

    private $watcher;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $tmp = $this;

        $this->watcher = new Watcher;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $libraries = Library::all();
        if ($libraries->count() == 0)
            $this->error('There are currently no libraries to watch - consider adding a couple.');

        // add watches to the root of the libraries
        foreach ($libraries as $library) {
            $libraryPath = $library->getPath();

            $libraryWd = $this->watcher->track($libraryPath, null, false, true);

            // if unsuccessful, just ignore and try to watch other directories
            if ($libraryWd === false) {
                $this->error('Unable to watch path: \'' . $libraryPath . '\'');
                continue;
            }
        }

        $this->watcher->go();
    }
}
