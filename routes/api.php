<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware(['api',InitializeTenancyByDomain::class,PreventAccessFromCentralDomains::class,])->group(function () {
    Route::get('/whoami', function () {
        return app(\Stancl\Tenancy\Contracts\Tenant::class);
    });
});
