<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Ropa', 'descripcion' => 'Todo tipo de prendas de vestir'],
            ['nombre' => 'Calzado', 'descripcion' => 'Zapatos, zapatillas, botas, etc.'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Bolsos, gorras, cinturones, etc.'],
            ['nombre' => 'Deportes', 'descripcion' => 'Artículos para práctica deportiva'],
            ['nombre' => 'Hogar', 'descripcion' => 'Productos para el hogar'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
