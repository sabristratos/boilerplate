<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-notification-digests --frequency=daily')->dailyAt('9:00');
Schedule::command('app:send-notification-digests --frequency=weekly')->weeklyOn(1, '9:00');
