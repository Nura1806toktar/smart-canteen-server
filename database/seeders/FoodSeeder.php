<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('foods')->insert([
            [
                'category_id' => 1,
                'name' => 'Borscht',
                'description' => 'Traditional beet soup',
                'price' => 1200,
                'available' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Chicken Plov',
                'description' => 'Rice with chicken and vegetables',
                'price' => 1800,
                'available' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Greek Salad',
                'description' => 'Tomatoes, cucumbers, feta cheese',
                'price' => 1000,
                'available' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Coca Cola',
                'description' => 'Cold drink',
                'price' => 500,
                'available' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Cheesecake',
                'description' => 'Cream cheese dessert',
                'price' => 900,
                'available' => true
            ],
        ]);
    }
}
