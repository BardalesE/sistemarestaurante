<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir el símbolo de moneda con TODAS las vistas
        // Usamos un try-catch para evitar errores si la tabla settings aún no existe (durante migraciones)
        try {
            if (Schema::hasTable('settings')) {
                $currency = Cache::remember('setting.currency_symbol', 3600, function () {
                    return Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
                });
                View::share('currency', $currency);
            }
        } catch (\Exception $e) {
            View::share('currency', 'S/');
        }
    }
}