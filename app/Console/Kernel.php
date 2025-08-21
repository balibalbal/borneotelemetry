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
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // protected function schedule(Schedule $schedule)
    // {
    //     // $schedule->command('inspire')->hourly();
    // }

    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('get:data-position-hso')->everyFourMinutes();

        // $schedule->command('geofenceHso:check')->everyThreeMinutes();
        // $schedule->command('send:data-to-hso')->everyThreeMinutes();
        // $schedule->command('parkingHSO:check')->everyFourMinutes();
        // $schedule->command('send:data-to-gps')->everyFourMinutes();
        // $schedule->command('send:data-to-login')->everyFourMinutes();
        // $schedule->command('information:delete-old')->daily(); // di jalankan setiap pukul 00:00
        $schedule->command('histories:update-address')->everyTwoMinutes();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
