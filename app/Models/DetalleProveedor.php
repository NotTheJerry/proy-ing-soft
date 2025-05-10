<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleProveedor extends Model
{
    protected $table = 'detalles_proveedor';
    protected $primaryKey = 'id_detalle_proveedor';
    
    protected $fillable = [
        'proveedor_id',
        'producto_id',
        'precio',
        'cantidad_minima'
    ];
    
    protected $casts = [
        'precio' => 'decimal:2'
    ];
    
    /**
     * Obtiene el proveedor al que pertenece este detalle.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id_proveedor');
    }
    
    /**
     * Obtiene el producto asociado a este detalle.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
}
