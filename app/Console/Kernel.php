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
        Commands\ExpiredGRReminder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:mail_expired_gr')->dailyAt('05:00');
        $schedule->command('cron:sync_master_data')->dailyAt('05:00');
        $schedule->command('cron:receive_pid_sap')->dailyAt('05:00');
        $schedule->command('cron:receive_pid_sap_response')->dailyAt('05:00');
        $schedule->command('cron:receive_po_sap')->everyFiveMinutes();
        $schedule->command('cron:receive_transaction_sap_response')->everyFiveMinutes();
        $schedule->command('cron:remove_locked_GR')->everyMinute();
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
