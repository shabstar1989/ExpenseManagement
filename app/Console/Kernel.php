<?php

namespace App\Console;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;


class Kernel extends ConsoleKernel
{
protected function schedule(Schedule $schedule)
{
    $schedule->command('payment:auto')->dailyAt('00:00'); 
}


}