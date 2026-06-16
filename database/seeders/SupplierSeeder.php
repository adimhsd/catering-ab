<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'nama_supplier' => 'UD. Pasar Segar',
                'pic'           => 'Bapak Ahmad',
                'wa'            => '081234567890',
                'alamat'        => 'Jl. Pasar Induk No. 12, Cirebon',
                'status'        => true,
            ],
            [
                'nama_supplier' => 'CV. Berkah Tani',
                'pic'           => 'Ibu Siti Rahayu',
                'wa'            => '082345678901',
                'alamat'        => 'Jl. Pertanian No. 5, Kuningan',
                'status'        => true,
            ],
            [
                'nama_supplier' => 'Toko Sembako Maju',
                'pic'           => 'Bapak Hendra',
                'wa'            => '083456789012',
                'alamat'        => 'Jl. Raya Ciledug No. 88, Cirebon',
                'status'        => true,
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(
                ['nama_supplier' => $data['nama_supplier']],
                $data
            );
        }
    }
}
