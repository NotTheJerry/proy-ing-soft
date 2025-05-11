<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                "id_cliente" => 2,
                "nombre" => "Cliente",
                "direccion" => "Calle Falsa 123",
                "correo_electronico" => "a@gmail.com",
                "telefono" => "123456789",
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
