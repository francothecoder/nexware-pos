<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Product::firstOrCreate([
            'sku' => 'MUG-001',
        ], [
            'name' => 'Nexware Ceramic Mug',
            'barcode' => '1002001',
            'description' => 'A branded ceramic mug for coffee and tea.',
            'price' => 32.50,
            'cost' => 18.00,
            'stock_quantity' => 18,
            'low_stock_level' => 8,
            'is_active' => true,
        ]);

        Product::firstOrCreate([
            'sku' => 'BAG-023',
        ], [
            'name' => 'Retail Shopping Bag',
            'barcode' => '1002002',
            'description' => 'Durable reusable shopping bag.',
            'price' => 18.00,
            'cost' => 7.00,
            'stock_quantity' => 6,
            'low_stock_level' => 4,
            'is_active' => true,
        ]);
    }
}
