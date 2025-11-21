<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use App\Support\HomeCache;

trait ClearsHomeCache
{
    protected static function bootClearsHomeCache(): void
    {
        static::saved(function() {
            HomeCache::forgetAll();
        });
        
        static::deleted(function() {
            HomeCache::forgetAll();
        });
    }
}