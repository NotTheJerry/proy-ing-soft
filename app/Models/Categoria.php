<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    
    protected $fillable = [
        'nombre',
        'descripcion',
    ];
    
    /**
     * Obtiene los productos asociados a esta categoría.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'id_categoria');
    }
}
