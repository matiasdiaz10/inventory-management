<?php

namespace Database\Seeders;

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
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'is_customer' => false, // Para saber que clientes van a poder utilizar los pedidos, en este caso admin
        ]);

        User::factory()->create([
            'name' => 'Cliente 1',
            'email' => 'cliente1@gmail.com',
            'is_customer' => true,
        ]);

        User::factory()->create([
            'name' => 'Cliente 2',
            'email' => 'cliente2@gmail.com',
            'is_customer' => true,
        ]);
    }
}
