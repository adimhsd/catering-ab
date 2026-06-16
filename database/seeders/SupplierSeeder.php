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

        $injectedNames = [
            'PAK ALI',
            'PAK DIDI TAHU',
            'PAK DIDI PISANG',
            'PAK SAMSURI',
            'PAK WASOLI',
            'BU ZUBAEDAH',
            'BU ALIMAH',
            'MBAK FAT',
            'SUMBER TELOR',
            'MAS AJIS',
            'PAK EDI',
            'MAS ALFIAN',
            'PAK EDI KANCI',
            'PAK Hj ACU',
            'BU HJ JUJU',
            'YUSEN',
            'BERAS'
        ];

        foreach ($injectedNames as $name) {
            Supplier::firstOrCreate(
                ['nama_supplier' => $name],
                [
                    'pic'           => $name,
                    'wa'            => '08123456789',
                    'alamat'        => 'Cirebon',
                    'status'        => true,
                ]
            );
        }
    }
}
