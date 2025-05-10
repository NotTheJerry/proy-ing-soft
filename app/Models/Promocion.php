<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';
    protected $primaryKey = 'id_promocion';
    
    protected $fillable = [
        'descripcion',
        'descuento',
        'fecha_inicio',
        'fecha_fin',
        'producto_id'
    ];
    
    protected $casts = [
        'descuento' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];
    
    /**
     * Obtiene el producto asociado a esta promoción.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
}
