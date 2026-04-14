<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
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
        $category2 = Category::create([
            'name' => 'Verdura',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
        $category3 = Category::create([
            'name' => 'Carne',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);

        //subcategories
        $subcategory1 = Subcategory::create([
            'category_id' => 1, // fruta
            'name' => 'Manzana',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
        $subcategory2 = Subcategory::create([
            'category_id' => 2, // verdura
            'name' => 'Fruta',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
    }
}
