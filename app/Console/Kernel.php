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
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update_prices_from_provider')->everyThirtyMinutes();
        $schedule->command('update_alquimia_tokens_command')->everyFiveMinutes();
        $schedule->command('update_alquimia_accounts_id')->everyThreeMinutes();
        $schedule->command('clean_temps_files')->everyThreeHours();
        $schedule->command('load_retroactive_alquimia_transactions')->everyTenMinutes();
        // $schedule->command('update_transaction_state')->everyMinute();
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
