<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Console\Scheduling\Schedule;

// Schedule the command to deactivate expired banners.
// Laravel normally schedules in Console\Kernel, but in this project we add it here
// so the scheduler can pick it up if the app expects schedules in routes/console.php.
// Note: scheduling is now handled in `app/Console/Kernel.php` following Laravel v12 conventions.
