<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('send:email')->daily();
        // torun test localy
        // php artisan schedule:work
        if(env('RMKT_EMAIL',false)){
            $schedule->command('app:send-remarketing-email')
                ->timezone('Asia/Bangkok')->dailyAt('12:00')
                ->emailOutputTo('maggotgluon@gmail.com');
        }
        if(env('RMKTBADGE_EMAIL',false)){
            $schedule->command('app:send-remarketing-badge')
                ->timezone('Asia/Bangkok')->dailyAt('12:00')
                ->emailOutputTo('maggotgluon@gmail.com');
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
