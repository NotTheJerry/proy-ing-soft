<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'cliente_id',
        'fecha_venta',
        'total_venta',
        'metodo_pago',
        'empleado_id'
    ];
    
    protected $casts = [
        'fecha_venta' => 'datetime',
        'total_venta' => 'decimal:2'
    ];
    
    /**
     * Obtiene el cliente (usuario) al que pertenece esta venta.
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }
    
    /**
     * Obtiene los detalles de esta venta.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id', 'id_venta');
    }
}
