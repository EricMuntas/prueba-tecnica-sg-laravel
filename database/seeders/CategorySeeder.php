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
        $category1 = Category::create([
            'name' => 'Fruta',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
    }
}
