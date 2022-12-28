<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('12345678')
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test Second',
            'email' => 'test2@example.com',
            'password' => bcrypt('12345678')
        ]);
    }
}
