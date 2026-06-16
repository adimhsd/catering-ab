<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Sayuran',
            'Daging',
            'Bumbu',
            'Sembako',
            'Buah',
            'Minuman',
            'Ikan & Seafood',
            'Telur & Susu',
            'Minyak & Lemak',
            'Rempah-rempah',
        ];

        foreach ($categories as $nama) {
            ProductCategory::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
