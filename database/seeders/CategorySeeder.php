<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::created([
            'name' => 'Ropa',
            'descripcion' => '-'
        ]);

        Category::created([
            'name' => 'Accesorios',
            'descripcion' => '-'
        ]);

        Category::created([
            'name' => 'Juguetes',
            'descripcion' => '-'
        ]);
    }
}
