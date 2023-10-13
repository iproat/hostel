<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {

        info('123');
        //$schedule->command('devicelog:import')->everyThirtyMinutes()->withoutOverlapping();
        //$schedule->command('devicelog:importmanual')->everyTwoMinutes()->withoutOverlapping();
        $schedule->command('report')->dailyAt('08:05');

        //$schedule->command('devicelog:import')->everyThirtyMinutes()->withoutOverlapping()->runInBackground();        
        /*$schedule->command('import:deviceemployee')->everyFifteenMinutes()->withoutOverlapping()->runInBackground();        
        $schedule->command('export:devicelog')->everyFiveMinutes()->withoutOverlapping()->runInBackground();        
        $schedule->command('employee:export')->everyTwoMinutes()->withoutOverlapping()->runInBackground();        
        $schedule->command('database:backup')->daily()->withoutOverlapping()->runInBackground();*/


        //$schedule->command('report:cron')->everyMinute()->withoutOverlapping()->runInBackground();        
        //$schedule->command('export:attendance')->everyMinute()->withoutOverlapping()->runInBackground();        
        //$schedule->command('pushlog:sqlserver')->everyMinute()->withoutOverlapping()->runInBackground();


    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
