<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsHomeCache
{
    protected static function bootClearsHomeCache(): void
    {
        static::saved(function() {
            Cache::store('file')->clear();
        });
        
        static::deleted(function() {
            Cache::store('file')->clear();
        });
    }
}