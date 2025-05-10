<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    
    protected $fillable = [
        'nombre',
        'direccion',
        'correo_electronico',
        'telefono',
    ];
    
    /**
     * Obtiene las ventas asociadas a este cliente.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id', 'id_cliente');
    }
}
