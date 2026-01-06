<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    DB::table('news')
        ->where('is_urgent', 1)
        ->whereNull('deleted_at')
        ->where('publish_date', '<=', now()->subHours(2))
        ->update(['is_urgent' => 0]);
})->everyMinute();
