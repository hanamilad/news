<?php

namespace App\Traits;

use App\Support\HomeCache;

trait ClearsHomeCache
{
    protected static function bootClearsHomeCache(): void
    {
        static::saved(function () {
            HomeCache::forgetAll();
        });

        static::deleted(function () {
            HomeCache::forgetAll();
        });
    }
}
