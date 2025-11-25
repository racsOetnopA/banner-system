<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Console\Scheduling\Schedule;

// Schedule the command to deactivate expired banners.
// Laravel normally schedules in Console\Kernel, but this project registers the
// schedule here so the task is available even if `app/Console/Kernel.php` is
// not present. The scheduler will pick this up when `routes/console.php` is
// required in the application's console bootstrap.

if (app()->runningInConsole()) {
    $schedule = app(Schedule::class);

    // Run the artisan command daily at 02:00 and avoid overlapping runs.
    $schedule->command('banners:deactivate-expired')
        ->dailyAt('02:00')
        ->withoutOverlapping();
}
