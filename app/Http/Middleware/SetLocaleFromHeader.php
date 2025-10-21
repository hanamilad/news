<?php

namespace App\Http\Middleware;

use Closure;

class SetLocaleFromHeader
{
    public function handle($request, Closure $next)
    {
        $lang = $request->header('X-Language', 'ar');
        app()->setLocale($lang);

        return $next($request);
    }
}
