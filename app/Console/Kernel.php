<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Init::class,
        Commands\Scan::class,
        Commands\Watch::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (\Cache::tags(['config', 'heat'])->get('enabled', false) === true) {
            $cronExpression = \Cache::tags(['config', 'heat'])->get('cron', '@hourly');
            $schedule->job(new \App\Jobs\DecreaseHeats())->cron($cronExpression);
        }

        if (\Cache::tags(['config', 'image', 'scheduler'])->get('enabled', false) === true) {
            $cronExpression = \Cache::tags(['config', 'image', 'scheduler'])->get('cron', '@daily');
            $schedule->job(new \App\Jobs\CleanupImageDisk())->cron($cronExpression);
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
