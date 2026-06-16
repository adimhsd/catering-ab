<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        static $units = ['Kg', 'Gram', 'Liter', 'Pack', 'Dus', 'Buah'];
        return ['nama_satuan' => $this->faker->unique()->randomElement($units) ?: $this->faker->word()];
    }
}
