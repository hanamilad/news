<?php

use App\Models\News;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Stancl\Tenancy\Database\TenantScope;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $affected =News::withoutGlobalScope(TenantScope::class)
        ->where('is_urgent', true)
        ->where('publish_date', '<=', now()->subHours(2))
        ->update(['is_urgent' => false]);
})->everyMinute();
