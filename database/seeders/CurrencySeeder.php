<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => 1,
                'name' => 'US Dollar',
                'symbol' => 'USD',
                'exchange_rate' => 1.0000,
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.9200,
            ],
            [
                'id' => 3,
                'name' => 'Dominican Peso',
                'symbol' => 'DOP',
                'exchange_rate' => 60.2500,
            ],
            [
                'id' => 4,
                'name' => 'Mexican Peso',
                'symbol' => 'MXN',
                'exchange_rate' => 17.5000,
            ],
        ];

        foreach ($currencies as $curr) {
            \App\Models\Currency::updateOrCreate(['id' => $curr['id']], $curr);
        }
    }
}
