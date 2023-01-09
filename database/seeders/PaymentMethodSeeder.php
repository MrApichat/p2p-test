<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::factory()->create([
            'name' => 'bank_transfer',
        ]);
        PaymentMethod::factory()->create([
            'name' => 'true_wallet',
        ]);
        PaymentMethod::factory()->create([
            'name' => 'shopee_pay'
        ]);
    }
}
