<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos las categorías y proveedores
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        
        if ($categorias->isEmpty() || $proveedores->isEmpty()) {
            $this->command->error('Debes crear primero categorías y proveedores');
            return;
        }
        
        $productos = [
            [
                'descripcion' => 'Camiseta de Algodón',
                'precio' => 19.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Ropa')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Textiles del Norte')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Blanco', 'Negro', 'Azul']),
                'tallas' => json_encode(['S' => true, 'M' => true, 'L' => true, 'XL' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Pantalón Vaquero',
                'precio' => 39.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Ropa')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Textiles del Norte')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Azul', 'Negro']),
                'tallas' => json_encode(['38' => true, '40' => true, '42' => true, '44' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Zapatillas Deportivas',
                'precio' => 59.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Calzado')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Calzados Europeos')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Blanco', 'Negro', 'Rojo']),
                'tallas' => json_encode(['39' => true, '40' => true, '41' => true, '42' => true, '43' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Bolso de Cuero',
                'precio' => 89.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Accesorios')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Accesorios Modernos')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Marrón', 'Negro', 'Beige']),
                'tallas' => json_encode(['Único' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Balón de Fútbol',
                'precio' => 24.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Deportes')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Deportes Total')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Multicolor']),
                'tallas' => json_encode(['5' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Juego de Sábanas',
                'precio' => 49.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Hogar')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Hogar & Decoración')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Blanco', 'Beige', 'Gris']),
                'tallas' => json_encode(['Individual' => true, 'Matrimonial' => true, 'Queen' => true, 'King' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Gorra Deportiva',
                'precio' => 14.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Accesorios')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Accesorios Modernos')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Negro', 'Azul', 'Rojo', 'Blanco']),
                'tallas' => json_encode(['Único' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Zapatos de Vestir',
                'precio' => 79.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Calzado')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Calzados Europeos')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Negro', 'Marrón']),
                'tallas' => json_encode(['39' => true, '40' => true, '41' => true, '42' => true, '43' => true, '44' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Chaqueta Impermeable',
                'precio' => 69.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Ropa')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Textiles del Norte')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Negro', 'Azul', 'Verde']),
                'tallas' => json_encode(['S' => true, 'M' => true, 'L' => true, 'XL' => true, 'XXL' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Set de Toallas',
                'precio' => 34.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Hogar')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Hogar & Decoración')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Blanco', 'Azul', 'Rosa', 'Gris']),
                'tallas' => json_encode(['Único' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Banda Elástica para Ejercicio',
                'precio' => 12.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Deportes')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Deportes Total')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Negro', 'Rojo', 'Verde']),
                'tallas' => json_encode(['Resistencia Baja' => true, 'Resistencia Media' => true, 'Resistencia Alta' => true]),
                'fecha_creacion' => now(),
            ],
            [
                'descripcion' => 'Cinturón de Cuero',
                'precio' => 29.99,
                'estado' => 'activo',
                'categoria_id' => $categorias->where('nombre', 'Accesorios')->first()->id_categoria ?? $categorias->first()->id_categoria,
                'proveedor_id' => $proveedores->where('nombre', 'Accesorios Modernos')->first()->id_proveedor ?? $proveedores->first()->id_proveedor,
                'colores' => json_encode(['Negro', 'Marrón']),
                'tallas' => json_encode(['90' => true, '95' => true, '100' => true, '105' => true, '110' => true]),
                'fecha_creacion' => now(),
            ],
        ];
        
        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
