<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';
    
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo_electronico',
    ];
    
    /**
     * Obtiene los productos asociados a este proveedor.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedor_id', 'id_proveedor');
    }
    
    /**
     * Obtiene los detalles del proveedor.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleProveedor::class, 'proveedor_id', 'id_proveedor');
    }
}
