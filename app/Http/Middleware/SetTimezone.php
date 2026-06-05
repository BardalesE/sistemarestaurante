<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class SetTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $timezone = Cache::remember('setting.timezone', 3600, function () {
                return Setting::where('key', 'timezone')->value('value');
            });

            if ($timezone) {
                Config::set('app.timezone', $timezone);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // BD no disponible aún (migraciones)
        }

        return $next($request);
    }
}