<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. TARJETAS DE RESUMEN (HOY)
        $totalSalesToday = Order::whereDate('created_at', $today)
                                ->where('status', 'completed')
                                ->sum('total');
                                
        $ordersCountToday = Order::whereDate('created_at', $today)
                                 ->where('status', 'completed')
                                 ->count();

        $newClients = Client::whereMonth('created_at', Carbon::now()->month)->count();

        // 2. GRÁFICO DE VENTAS (Últimos 7 días) - LÓGICA CORREGIDA
        $startDate = Carbon::now()->subDays(6)->startOfDay(); // Hace 7 días (incluyendo hoy)
        $endDate = Carbon::now()->endOfDay();

        // Agrupamos estrictamente por la parte "DATE" de la columna
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total) as total')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Preparamos los arrays para el gráfico
        $chartLabels = [];
        $chartValues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dateString = $day->format('Y-m-d'); // Ej: 2025-12-29
            
            // Etiqueta visual (Ej: "Lun 29")
            $chartLabels[] = ucfirst($day->locale('es')->isoFormat('ddd D'));
            
            // Buscamos si hubo venta ese día específico
            $sale = $salesData->first(function($item) use ($dateString) {
                return $item->date === $dateString;
            });
            
            $chartValues[] = $sale ? $sale->total : 0;
        }

        // 3. PRODUCTOS MÁS VENDIDOS (TOP 5)
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('products.name', 'products.image', DB::raw('SUM(order_details.quantity) as total_qty'))
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. ALERTAS DE STOCK CRÍTICO (Menos de 10 unidades)
        $lowStockProducts = Product::whereNotNull('stock')
            ->where('stock', '<=', 10)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // 5. Moneda
        $currency = \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';

        return view('dashboard', compact(
            'totalSalesToday', 'ordersCountToday', 'newClients', 
            'chartLabels', 'chartValues', 
            'topProducts', 'lowStockProducts', 'currency'
        ));
    }
}