<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedProducts = [
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'XL',
                'colour' => 'black',
            ],
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'S',
                'colour' => 'green',
            ],
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'XL',
                'colour' => 'lime',
            ],
        ];

        foreach ($seedProducts as $seedProduct) {
            $product = Product::create($seedProduct);
        }
    }
}
