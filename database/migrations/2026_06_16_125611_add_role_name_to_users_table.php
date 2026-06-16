<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom 'nama' (alias PRD) dan 'role' ke tabel users.
     * Kolom 'name' bawaan Laravel dipertahankan untuk kompatibilitas Breeze.
     * Kolom 'nama' adalah nama tampilan sesuai PRD.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom nama sesuai PRD (setelah kolom name bawaan Breeze)
            $table->string('nama')->after('name')->nullable();
            // Role: admin_dapur atau kepala_divisi
            $table->enum('role', ['admin_dapur', 'kepala_divisi'])
                  ->default('admin_dapur')
                  ->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'role']);
        });
    }
};
