<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Textiles del Norte',
                'direccion' => 'Calle Principal 123, Ciudad Industrial',
                'telefono' => '555-123-456',
                'correo_electronico' => 'contacto@textilesdelnorte.com',
            ],
            [
                'nombre' => 'Calzados Europeos',
                'direccion' => 'Avenida de la Moda 456, Sector Comercial',
                'telefono' => '555-789-012',
                'correo_electronico' => 'ventas@calzadoseuropeos.com',
            ],
            [
                'nombre' => 'Accesorios Modernos',
                'direccion' => 'Calle Diseño 789, Barrio Artístico',
                'telefono' => '555-345-678',
                'correo_electronico' => 'info@accesoriosmodernos.com',
            ],
            [
                'nombre' => 'Deportes Total',
                'direccion' => 'Avenida Olímpica 234, Zona Deportiva',
                'telefono' => '555-901-234',
                'correo_electronico' => 'deportes@deportestotal.com',
            ],
            [
                'nombre' => 'Hogar & Decoración',
                'direccion' => 'Plaza Central 567, Centro Comercial',
                'telefono' => '555-567-890',
                'correo_electronico' => 'ventas@hogardecoracion.com',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
