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
            'email'    => 'admin@catering.test',
            'password' => Hash::make('password'),
            'role'     => 'admin_dapur',
        ]);

        // Kepala Divisi — pengguna manajerial
        User::create([
            'name'     => 'Kepala Divisi',
            'nama'     => 'Kepala Divisi Catering',
            'email'    => 'kepala@catering.test',
            'password' => Hash::make('password'),
            'role'     => 'kepala_divisi',
        ]);
    }
}
