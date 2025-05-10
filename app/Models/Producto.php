<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    
    protected $fillable = [
        'descripcion',
        'precio',
        'categoria_id',
        'tallas',
        'colores',
        'proveedor_id',
        'fecha_creacion',
        'estado',
    ];
    
    protected $casts = [
        'tallas' => 'json',
        'colores' => 'json',
        'fecha_creacion' => 'date',
        'precio' => 'decimal:2'
    ];
    
    /**
     * Obtiene la categoría a la que pertenece este producto.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }
    
    /**
     * Obtiene el proveedor que suministra este producto.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id_proveedor');
    }
    
    /**
     * Obtiene los detalles de proveedores asociados a este producto.
     */
    public function detallesProveedores()
    {
        return $this->hasMany(DetalleProveedor::class, 'producto_id', 'id_producto');
    }
    
    /**
     * Obtiene los detalles de ventas asociados a este producto.
     */
    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id', 'id_producto');
    }
    
    /**
     * Obtiene las promociones asociadas a este producto.
     */
    public function promociones()
    {
        return $this->hasMany(Promocion::class, 'producto_id', 'id_producto');
    }
    
    /**
     * Obtiene el inventario asociado a este producto.
     */
    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'producto_id', 'id_producto');
    }
}
