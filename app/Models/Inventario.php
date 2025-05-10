<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $primaryKey = 'id_inventario';
    
    protected $fillable = [
        'producto_id',
        'cantidad_disponible',
        'fecha_ultima_actualizacion'
    ];
    
    protected $casts = [
        'fecha_ultima_actualizacion' => 'date'
    ];
    
    /**
     * Obtiene el producto asociado a este inventario.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
}
