<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p1 = \App\Models\Product::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'MacBook Pro 16" M3 Max',
                'description' => 'Laptop para desarrollo de alto rendimiento con 36GB RAM y 1TB SSD',
                'price' => 3499.00,
                'currency_id' => 1, // USD
                'tax_cost' => 244.93,
                'manufacturing_cost' => 1800.00,
            ]
        );

        $p1->prices()->updateOrCreate(
            ['currency_id' => 2],
            ['price' => 3219.08] // EUR
        );
        $p1->prices()->updateOrCreate(
            ['currency_id' => 3],
            ['price' => 210814.75] // DOP
        );

        $p2 = \App\Models\Product::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Monitor Dell UltraSharp 27" 4K',
                'description' => 'Monitor IPS Black con hub USB-C y 98% DCI-P3',
                'price' => 599.99,
                'currency_id' => 1, // USD
                'tax_cost' => 42.00,
                'manufacturing_cost' => 280.00,
            ]
        );

        $p2->prices()->updateOrCreate(
            ['currency_id' => 2],
            ['price' => 551.99] // EUR
        );
    }
}
