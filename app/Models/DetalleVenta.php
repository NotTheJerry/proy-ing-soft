<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey = 'id_detalle_venta';
    
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
    
    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    /**
     * Obtiene la venta a la que pertenece este detalle.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id_venta');
    }
    
    /**
     * Obtiene el producto asociado a este detalle.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
    
}
