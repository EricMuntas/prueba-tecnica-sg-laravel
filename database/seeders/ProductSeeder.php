<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Fee;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::create([
            'name' => 'Manzana',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
        $product2 = Product::create([
            'name' => 'Lechuga',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);
        $product3 = Product::create([
            'name' => 'Pera',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore sed saepe voluptatum enim nostrum tempora rerum amet, error soluta corporis provident repudiandae quia praesentium. Voluptatibus doloremque ex ipsam deserunt hic',
        ]);

        $arrayProducts = [$product1, $product2, $product3];

        $category = Category::first();

        $product1->categories()->attach($category->id);
        $product2->categories()->attach($category->id);
        $product3->categories()->attach($category->id);

        foreach ($arrayProducts as $product) {

            $startDate = Carbon::now()->subDays(7);
            $endDate = Carbon::now()->addDays(7);

            $faker = Faker::create();

            Fee::create([
                'product_id' => $product->id,
                'start_day' => $startDate,
                'end_day' => $endDate,
                'price' => $faker->numberBetween(1, 10),
                'created_at' => Carbon::now(),
            ]);
        }
    }
}
