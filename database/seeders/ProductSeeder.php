<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the product seeds.
     * This seeder truncates the products table and inserts the defined menu items.
     */
    public function run()
    {
        // Remove existing products (resets table contents)
        Product::truncate();

        // Define menu items to seed
        $items = [
            // Burgers
            ['name' => 'Classic Burger', 'price' => 120.00, 'image' => 'https://via.placeholder.com/200?text=Classic+Burger'],
            ['name' => 'Cheese Burger', 'price' => 140.00, 'image' => 'https://via.placeholder.com/200?text=Cheese+Burger'],
            ['name' => 'Bacon Burger', 'price' => 160.00, 'image' => 'https://via.placeholder.com/200?text=Bacon+Burger'],

            // Chicken
            ['name' => 'Fried Chicken', 'price' => 150.00, 'image' => 'https://via.placeholder.com/200?text=Fried+Chicken'],
            ['name' => 'Grilled Chicken', 'price' => 160.00, 'image' => 'https://via.placeholder.com/200?text=Grilled+Chicken'],

            // Sides
            ['name' => 'French Fries', 'price' => 60.00, 'image' => 'https://via.placeholder.com/200?text=French+Fries'],
            ['name' => 'Onion Rings', 'price' => 75.00, 'image' => 'https://via.placeholder.com/200?text=Onion+Rings'],

            // Drinks
            ['name' => 'Coke', 'price' => 50.00, 'image' => 'https://via.placeholder.com/200?text=Coke'],
            ['name' => 'Sprite', 'price' => 50.00, 'image' => 'https://via.placeholder.com/200?text=Sprite'],
            ['name' => 'Iced Tea', 'price' => 45.00, 'image' => 'https://via.placeholder.com/200?text=Iced+Tea'],
        ];

        // Insert items
        foreach ($items as $item) {
            Product::create($item);
        }
    }
}
