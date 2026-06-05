<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    // AQUI ESTABA EL PROBLEMA: Faltaba 'note'
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'status',
        'note' // <--- NUEVO CAMPO AUTORIZADO
    ];

    // Relación: Pertenece a una Orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // withTrashed() para que el historial muestre productos aunque estén eliminados
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}