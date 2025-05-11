<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "name" => "Cliente",
                "email" => "cliente@gmail.com",
                "password" => bcrypt("12345678"),
                "role" => "cliente",
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
