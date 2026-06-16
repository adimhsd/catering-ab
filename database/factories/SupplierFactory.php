<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'nama_supplier' => $this->faker->company(),
            'pic'           => $this->faker->name(),
            'wa'            => '08' . $this->faker->numerify('#########'),
            'alamat'        => $this->faker->address(),
            'status'        => true,
        ];
    }
}
