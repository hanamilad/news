<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\News::query()
        ->where('is_urgent', true)
        ->where('publish_date', '<=', now()->subHours(2))
        ->update(['is_urgent' => false]);
})->everyMinute();
