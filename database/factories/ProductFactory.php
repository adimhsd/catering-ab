<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nama_produk' => $this->faker->words(2, true),
            'category_id' => ProductCategory::factory(),
            'unit_id'     => Unit::factory(),
            'status'      => true,
        ];
    }
}
