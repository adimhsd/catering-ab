<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Dapur — pengguna operasional harian
        User::create([
            'name'     => 'Admin Dapur',
            'nama'     => 'Admin Dapur Al-Bahjah',
            'email'    => 'admin@cateringalbahjah.id',
            'password' => Hash::make('admin123!'),
            'role'     => 'admin_dapur',
        ]);

        // Kepala Divisi — pengguna manajerial
        User::create([
            'name'     => 'Kepala Divisi',
            'nama'     => 'Kepala Divisi Catering',
            'email'    => 'kadiv@cateringalbahjah.id',
            'password' => Hash::make('kadiv123!'),
            'role'     => 'kepala_divisi',
        ]);
    }
}
