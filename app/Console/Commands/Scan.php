<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \App\Library;

class Scan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mangapie:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans and updates the libraries for mangapie';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \DB::connection();
        } catch (Exception $e) {
            $this->error('Unable to establish a connection to the database.');
            $this->error('Please ensure your settings in .env are correct.');

            return;
        }

        $libraries = Library::all();
        if ($libraries->count() == 0) {
            $this->error('No libraries were found. Did you forget to add some?');

            return;
        }

        foreach ($libraries as $library) {
            $this->comment('Updating '. $library->getName());
            $library->scan();
        }

        $this->comment('Finished updating libraries');
    }
}
