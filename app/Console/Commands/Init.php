<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mangapie:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes mangapie';

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
        $this->comment('Generating app key...');
        // avoid generating a key if it already exists
        if (!config('app.key')) {
            Artisan::call('key:generate');
        } else {
            $this->comment('App key already exists. Skipping.');
        }

        $this->comment('Publishing vendor files...');
        if (\File::exists('config/image.php') == false) {
            Artisan::call('vendor:publish', ['--provider' => 'Intervention\\Image\\ImageServiceProviderLaravel5']);
        } else {
            $this->comment('Config file for Intervention\\Image already exists. Skipping.');
	    }

	    if (\File::exists('resources/views/vendor/pagination/bootstrap-4.blade.php') == false) {
            Artisan::call('vendor:publish', ['--provider' => 'Illuminate\\Pagination\\PaginationServiceProvider']);
	    } else {
            $this->comment('Vendor pagination blade already exists. Skipping.');
        }

        $this->comment('Migrating...');
        Artisan::call('migrate', ['--force' => true]);

        $this->comment('Seeding database...');
        Artisan::call('db:seed', ['--force' => true]);

        $this->comment('Finished initializing mangapie.');
    }
}
