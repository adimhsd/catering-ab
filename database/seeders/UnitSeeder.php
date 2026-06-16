<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            'Kg',
            'Gram',
            'Liter',
            'ML',
            'Pack',
            'Dus',
            'Buah',
            'Ikat',
            'Karung',
            'Botol',
            'Lembar',
            'Porsi',
        ];

        foreach ($units as $nama) {
            Unit::firstOrCreate(['nama_satuan' => $nama]);
        }
    }
}
