<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'fecha_venta',
        'total_venta',
        'metodo_pago',
        'cliente_id',
        'empleado_id',
    ];
    
    protected $casts = [
        'fecha_venta' => 'date',
        'total_venta' => 'decimal:2'
    ];
    
    /**
     * Obtiene el cliente al que pertenece esta venta.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
    
    /**
     * Obtiene los detalles de esta venta.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id', 'id_venta');
    }
}
