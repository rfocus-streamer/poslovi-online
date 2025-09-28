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
        $schedule->command('packages:check-expiry')
             ->dailyAt('03:00') // Pokreće se svakog dana u 3:00 ujutru
             ->onOneServer() // Važno ako koristite više servera
             ->timezone('Europe/Belgrade'); // Postavite odgovarajuću vremensku zonu

        // Dnevno slanje emailova u 10:30
        $schedule->command('emails:daily')
                 ->dailyAt('11:20')
                 ->onOneServer() // Važno ako koristite više servera
                 ->timezone('Europe/Belgrade'); // Prilagodite vašoj vremenskoj zoni

        $schedule->command('check:online-status')
            ->everyMinute()
            ->runInBackground() // Dodajte za paralelno izvršavanje
            ->withoutOverlapping(5); // Max 5 minuta izvršavanja
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
