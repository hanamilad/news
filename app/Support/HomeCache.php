<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class HomeCache
{
    protected static function tenantPrefix(): string
    {
        $prefix = '';
        if (function_exists('tenant')) {
            $tenantId = tenant('id');
            if ($tenantId) {
                $prefix = 'tenant_' . $tenantId . ':';
            }
        }
        return $prefix;
    }

    protected static function registryKey(): string
    {
        return self::tenantPrefix() . 'home_page_registry';
    }

    public static function registerKey(string $key): void
    {
        $store = Cache::store('file');
        $regKey = self::registryKey();
        $existing = $store->get($regKey, []);
        if (!in_array($key, $existing, true)) {
            $existing[] = $key;
            // store forever so the registry outlives individual entries
            $store->forever($regKey, $existing);
        }
    }

    public static function forgetAll(): void
    {
        $store = Cache::store('file');
        $regKey = self::registryKey();
        $keys = $store->get($regKey, []);
        foreach ($keys as $key) {
            $store->forget($key);
        }
        // reset the registry
        $store->forget($regKey);
    }
}