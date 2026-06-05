<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\InventoryLog;
use App\Models\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $areas = Area::with(['tables' => function($q) {
            $q->with(['orders' => function($q) {
                $q->where('status', 'pending');
            }, 'reservations' => function($q) {
                $q->where('status', 'confirmed')
                  ->whereDate('reservation_time', Carbon::today())
                  ->where('reservation_time', '>=', Carbon::now()->subHours(2)) 
                  ->orderBy('reservation_time', 'asc');
            }]);
        }])->get();
        
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        return view('pos.index', compact('areas', 'currency'));
    }

    public function order(Table $table)
    {
        // --- AQUÍ ESTÁ EL FILTRO MÁGICO ---
        // Solo traemos productos que estén ACTIVOS y SEAN VENDIBLES (is_saleable = true)
        $categories = Category::with(['products' => function($q) {
            $q->where('is_active', true)
              ->where('is_saleable', true); // <--- ESTO OCULTA LA CARNE
        }])->where('is_active', true)->get();

        $order = Order::where('table_id', $table->id)->where('status', 'pending')->with('details.product')->first();
        $occupiedTableIds = Order::where('status', 'pending')->pluck('table_id');
        $freeTables = Table::whereNotIn('id', $occupiedTableIds)->where('id', '!=', $table->id)->with('area')->get();
        $clients = Client::select('id', 'name', 'document_number')->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';

        return view('pos.order', compact('table', 'categories', 'order', 'freeTables', 'clients', 'currency'));
    }

    public function addToOrder(Request $request, Table $table)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $product = Product::findOrFail($request->product_id);
        DB::transaction(function() use ($table, $product) {
            $order = Order::firstOrCreate(
                ['table_id' => $table->id, 'status' => 'pending'],
                ['user_id' => auth()->id() ?? 1, 'total' => 0]
            );
            if ($order->wasRecentlyCreated) {
                $table->update(['status' => 'occupied']);
            }
            $detail = $order->details()->where('product_id', $product->id)->first();
            if ($detail) $detail->increment('quantity');
            else $order->details()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => $product->price, 'status' => 'pending']);
            $this->recalculateTotal($order);
        });
        return $this->getCartHtml($table);
    }

    public function updateQuantity(Request $request, OrderDetail $detail)
    {
        $newQty = $request->quantity;
        if ($newQty < 1) { $order = $detail->order; $detail->delete(); $this->recalculateTotal($order); } 
        else { $detail->update(['quantity' => $newQty]); $this->recalculateTotal($detail->order); }
        return redirect()->back(); 
    }

    public function updateNote(Request $request, OrderDetail $detail) { $detail->update(['note' => $request->note]); return redirect()->back(); }
    public function removeItem(OrderDetail $detail) { $order = $detail->order; $detail->delete(); $this->recalculateTotal($order); return redirect()->back(); }
    public function applyDiscount(Request $request, Order $order) { $order->discount = $request->input('discount', 0); $order->tip = $request->input('tip', 0); $order->save(); $this->recalculateTotal($order); return redirect()->back(); }
    
    public function moveTable(Request $request, Order $order) {
        $request->validate(['target_table_id' => 'required|exists:tables,id']);
        if (Order::where('table_id', $request->target_table_id)->where('status', 'pending')->exists()) return redirect()->back()->with('error', 'Ocupada.');
        $order->table_id = $request->target_table_id; $order->save();
        return redirect()->route('pos.order', $request->target_table_id);
    }

    public function getSplitContent(Order $order) { return view('pos.partials.split_content', compact('order')); }

    public function processSplit(Request $request, Order $order)
    {
        $request->validate([
            'selected_items'   => 'required|array|min:1',
            'selected_items.*' => 'exists:order_details,id',
            'payment_method'   => 'required|in:cash,card',
        ]);

        $selectedDetails = $order->details()
            ->whereIn('id', $request->selected_items)
            ->with('product.ingredients')
            ->get();

        if ($selectedDetails->isEmpty()) {
            return back()->with('error', 'No hay ítems válidos seleccionados.');
        }

        $splitOrderId = null;

        DB::transaction(function () use ($order, $selectedDetails, $request, &$splitOrderId) {
            $splitTotal = $selectedDetails->sum(fn($d) => $d->price * $d->quantity);

            $splitOrder = Order::create([
                'table_id'        => $order->table_id,
                'user_id'         => $order->user_id,
                'status'          => 'completed',
                'total'           => $splitTotal,
                'payment_method'  => $request->payment_method,
                'received_amount' => $splitTotal,
                'change_amount'   => 0,
                'document_type'   => 'Ticket',
                'client_name'     => 'División — Orden #' . $order->id,
                'discount'        => 0,
                'tip'             => 0,
            ]);

            $splitOrderId = $splitOrder->id;

            // Mover ítems seleccionados a la nueva orden completada
            OrderDetail::whereIn('id', $selectedDetails->pluck('id'))
                ->update(['order_id' => $splitOrder->id]);

            // Recalcular total de la orden original con los ítems restantes
            $order->load('details');
            $this->recalculateTotal($order);

            // Descontar inventario (igual que checkout, sin bajar de cero)
            foreach ($selectedDetails as $detail) {
                $product    = $detail->product;
                $ingredients = $product->ingredients;

                if ($ingredients->count() > 0) {
                    foreach ($ingredients as $ingredient) {
                        if (is_null($ingredient->stock)) continue;
                        $qty = min($ingredient->pivot->quantity * $detail->quantity, max(0, $ingredient->stock));
                        if ($qty <= 0) continue;
                        $oldStock = $ingredient->stock;
                        $ingredient->decrement('stock', $qty);
                        InventoryLog::create([
                            'product_id' => $ingredient->id,
                            'user_id'    => Auth::id(),
                            'type'       => 'sale',
                            'quantity'   => -$qty,
                            'old_stock'  => $oldStock,
                            'new_stock'  => $oldStock - $qty,
                            'note'       => 'División: ' . $product->name . ' (Orden #' . $order->id . ')',
                        ]);
                    }
                } else {
                    if (!is_null($product->stock)) {
                        $qty = min($detail->quantity, max(0, $product->stock));
                        if ($qty <= 0) continue;
                        $oldStock = $product->stock;
                        $product->decrement('stock', $qty);
                        InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id'    => Auth::id(),
                            'type'       => 'sale',
                            'quantity'   => -$qty,
                            'old_stock'  => $oldStock,
                            'new_stock'  => $oldStock - $qty,
                            'note'       => 'División POS #' . $order->id,
                        ]);
                    }
                }
            }
        });

        // Si quedan ítems en la orden original, volver a ella; si no, ir al POS
        $order->load('details');
        if ($order->details->isNotEmpty()) {
            return redirect()->route('pos.order', $order->table_id)
                ->with('success', 'Pago parcial registrado. Quedan ' . $order->details->count() . ' ítem(s) pendientes.');
        }

        return redirect()->route('pos.index')->with('success', 'Cuenta dividida y cerrada completamente.');
    }
    public function precheck(Order $order) { $settings = Setting::pluck('value', 'key')->toArray(); return view('sales.ticket', compact('order', 'settings')); }
    public function kitchenTicket(Order $order) { return view('sales.kitchen_ticket', compact('order')); }

    public function checkout(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('pos.index')->with('error', 'Orden ya cerrada.');
        }

        $order->load('details');
        if ($order->details->isEmpty()) {
            return redirect()->back()->with('error', 'La orden no tiene productos.');
        }

        $request->validate([
            'payment_method'  => 'required|in:cash,card',
            'received_amount' => 'nullable|numeric|min:0',
        ]);

        $method   = $request->input('payment_method', 'cash');
        $received = $method === 'cash'
            ? (float) $request->input('received_amount', $order->total)
            : $order->total;

        if ($method === 'cash' && $received < $order->total) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El monto recibido es menor al total de la orden.');
        }

        $change    = max(0, $received - $order->total);
        $clientId  = $request->input('client_id') ?: null;
        $clientObj = $clientId ? Client::find($clientId) : null;
        $clientName = $clientObj?->name ?? $request->input('client_name', 'Público');

        DB::transaction(function () use ($order, $method, $received, $change, $request, $clientId, $clientName) {
            $order->update([
                'status'          => 'completed',
                'payment_method'  => $method,
                'received_amount' => $received,
                'change_amount'   => $change,
                'document_type'   => $request->input('document_type', 'Ticket'),
                'client_id'       => $clientId,
                'client_name'     => $clientName,
                'client_document' => $request->input('client_document'),
            ]);

            foreach ($order->details as $detail) {
                $product     = $detail->product;
                $ingredients = $product->ingredients;

                if ($ingredients->count() > 0) {
                    foreach ($ingredients as $ingredient) {
                        if (is_null($ingredient->stock)) continue;
                        // Nunca bajar de cero
                        $qty = min($ingredient->pivot->quantity * $detail->quantity, max(0, $ingredient->stock));
                        if ($qty <= 0) continue;
                        $oldStock = $ingredient->stock;
                        $ingredient->decrement('stock', $qty);
                        InventoryLog::create([
                            'product_id' => $ingredient->id,
                            'user_id'    => Auth::id(),
                            'type'       => 'sale',
                            'quantity'   => -$qty,
                            'old_stock'  => $oldStock,
                            'new_stock'  => $oldStock - $qty,
                            'note'       => 'Venta: ' . $product->name . ' (Orden #' . $order->id . ')',
                        ]);
                    }
                } else {
                    if (!is_null($product->stock)) {
                        $qty = min($detail->quantity, max(0, $product->stock));
                        if ($qty <= 0) continue;
                        $oldStock = $product->stock;
                        $product->decrement('stock', $qty);
                        InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id'    => Auth::id(),
                            'type'       => 'sale',
                            'quantity'   => -$qty,
                            'old_stock'  => $oldStock,
                            'new_stock'  => $oldStock - $qty,
                            'note'       => 'Venta POS #' . $order->id,
                        ]);
                    }
                }
            }
        });

        // Liberar la mesa al completar el pago
        Table::where('id', $order->table_id)->update(['status' => 'available']);

        return redirect()->route('pos.index')->with('success', 'Venta registrada correctamente.');
    }

    private function recalculateTotal(Order $order)
    {
        $subtotal = $order->details->sum(fn($d) => $d->price * $d->quantity);
        $total = ($subtotal - ($order->discount ?? 0)) + ($order->tip ?? 0);
        $order->update(['total' => max(0, $total)]);
    }

    private function getCartHtml(Table $table)
    {
        $order = Order::where('table_id', $table->id)->where('status', 'pending')->with('details.product')->first();
        $clients = Client::select('id', 'name', 'document_number')->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        return view('pos.partials.cart', compact('order', 'clients', 'currency'))->render();
    }
}