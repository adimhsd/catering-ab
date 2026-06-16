<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        static $categories = ['Sayuran', 'Daging', 'Bumbu', 'Sembako', 'Buah', 'Minuman'];
        return ['nama_kategori' => $this->faker->unique()->randomElement($categories) ?: $this->faker->word()];
    }
}
