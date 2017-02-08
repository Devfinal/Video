<?php

namespace App\Console;

use App\Console\Commands\CheckSubscriptions;
use App\Console\Commands\CleanupMedia;
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
        CheckSubscriptions::class,
        CleanupMedia::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('subscriptions:check')
             ->hourly()
             ->withoutOverlapping();

        $schedule->command('check-canceled-subscriptions')
            ->hourly();

        $schedule->command('check-deactivated-blogs')
            ->hourly();

        $schedule->command('cleanup:media')
            ->daily()
            ->at('04:00');

        $schedule->command('reset-view-counters')
            ->mondays()
            ->at('00:00');
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
