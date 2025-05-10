<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';
    protected $primaryKey = 'id_reporte';
    
    protected $fillable = [
        'tipo_reporte',
        'fecha_generacion',
        'contenido'
    ];
    
    protected $casts = [
        'fecha_generacion' => 'date'
    ];
}
