<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Currency::factory()->create([
            'name' => 'USDT',
            'type' => 'coin'
        ]);
        Currency::factory()->create([
            'name' => 'BUSD',
            'type' => 'coin'
        ]);
        Currency::factory()->create([
            'name' => 'BNB',
            'type' => 'coin'
        ]);
        Currency::factory()->create([
            'name' => 'BTC',
            'type' => 'coin'
        ]);
        Currency::factory()->create([
            'name' => 'ETH',
            'type' => 'coin'
        ]);
        Currency::factory()->create([
            'name' => 'USD',
            'type' => 'fiat'
        ]);
        Currency::factory()->create([
            'name' => 'THB',
            'type' => 'fiat'
        ]);
    }
}
