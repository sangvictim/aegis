<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'activated' => true
        ]);
        User::factory()->create([
            'name' => 'Kasir',
            'email' => 'kasir@mail.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'activated' => true
        ]);

        Product::factory(10)->create()->each(function ($product) {
            $product->stock()->create([
                'stock' => 50
            ]);
        });
    }
}
