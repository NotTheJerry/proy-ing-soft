<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = Producto::all();
        
        if ($productos->isEmpty()) {
            $this->command->error('Debes crear primero productos');
            return;
        }
        
        foreach ($productos as $producto) {
            // Generar algunas cantidades aleatorias para simular diferentes niveles de stock
            // Algunos productos con stock normal, otros con stock bajo y otros agotados
            $cantidades = [0, 3, 5, 8, 15, 25, 40, 50, 75, 100];
            $cantidad = $cantidades[array_rand($cantidades)];
            
            Inventario::create([
                'producto_id' => $producto->id_producto,
                'cantidad_disponible' => $cantidad,
                'fecha_ultima_actualizacion' => now(),
            ]);
        }
    }
}
