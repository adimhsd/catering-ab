<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Map nama kategori ke ID
        $cat = ProductCategory::pluck('id', 'nama_kategori');
        $unit = Unit::pluck('id', 'nama_satuan');

        $products = [
            ['nama_produk' => 'Beras Premium',        'kategori' => 'Sembako',         'satuan' => 'Kg'],
            ['nama_produk' => 'Minyak Goreng',         'kategori' => 'Minyak & Lemak',  'satuan' => 'Liter'],
            ['nama_produk' => 'Daging Sapi',           'kategori' => 'Daging',          'satuan' => 'Kg'],
            ['nama_produk' => 'Daging Ayam',           'kategori' => 'Daging',          'satuan' => 'Kg'],
            ['nama_produk' => 'Bawang Merah',          'kategori' => 'Bumbu',           'satuan' => 'Kg'],
            ['nama_produk' => 'Bawang Putih',          'kategori' => 'Bumbu',           'satuan' => 'Kg'],
            ['nama_produk' => 'Cabai Merah',           'kategori' => 'Bumbu',           'satuan' => 'Kg'],
            ['nama_produk' => 'Tomat',                 'kategori' => 'Sayuran',         'satuan' => 'Kg'],
            ['nama_produk' => 'Kangkung',              'kategori' => 'Sayuran',         'satuan' => 'Ikat'],
            ['nama_produk' => 'Bayam',                 'kategori' => 'Sayuran',         'satuan' => 'Ikat'],
            ['nama_produk' => 'Telur Ayam',            'kategori' => 'Telur & Susu',    'satuan' => 'Buah'],
            ['nama_produk' => 'Gula Pasir',            'kategori' => 'Sembako',         'satuan' => 'Kg'],
            ['nama_produk' => 'Garam Halus',           'kategori' => 'Bumbu',           'satuan' => 'Pack'],
            ['nama_produk' => 'Tepung Terigu',         'kategori' => 'Sembako',         'satuan' => 'Kg'],
            ['nama_produk' => 'Air Mineral (Galon)',   'kategori' => 'Minuman',         'satuan' => 'Buah'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['nama_produk' => $p['nama_produk']],
                [
                    'category_id' => $cat[$p['kategori']],
                    'unit_id'     => $unit[$p['satuan']],
                    'status'      => true,
                ]
            );
        }
    }
}
