<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeCache
{
    protected static function tenantPrefix(): string
    {
        $prefix = '';
        if (function_exists('tenant')) {
            $tenantId = tenant('id');
            if ($tenantId) {
                $prefix = 'tenant_'.$tenantId.':';
            }
        }

        return $prefix;
    }

    protected static function registryKey(): string
    {
        return self::tenantPrefix().'home_page_registry';
    }

    public static function registerKey(string $key): void
    {
        $store = Cache::store('file');
        $regKey = self::registryKey();
        $existing = $store->get($regKey, []);
        if (! is_array($existing)) {
            $existing = [];
        }
        if (! in_array($key, $existing, true)) {
            $existing[] = $key;
            $store->put($regKey, $existing, 86400);
        }
    }

    public static function forgetAll(): void
    {
        try {
            $store = Cache::store('file');
            $regKey = self::registryKey();
            $keys = $store->get($regKey, []);

            if (is_array($keys)) {
                foreach ($keys as $key) {
                    $store->forget($key);
                }
            }
            $store->forget($regKey);
        } catch (\Exception $e) {
            Log::error('HomeCache forgetAll failed: '.$e->getMessage());
        }
    }
}
