<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Import::class,
        \App\Console\Commands\Touch::class,
        \App\Console\Commands\ImportHimoKeyword::class,
        \App\Console\Commands\ImportBk2Recommend::class,
        \App\Console\Commands\CreateReleaseCalendar::class,
        \App\Console\Commands\CheckHimoTables::class,
        \App\Console\Commands\CreateTopRelease::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // インポート処理
//        $schedule->command('import')->withoutOverlapping();
    }
}
