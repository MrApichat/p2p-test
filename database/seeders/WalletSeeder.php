<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Wallet;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 5) as $i) {
            foreach (range(1, 2) as $j) {
                Wallet::factory()->create([
                    'user_id' => $j,
                    'coin_id' => $i,
                    'total' => rand(50, 100)
                ]);
            }
        }
    }
}
