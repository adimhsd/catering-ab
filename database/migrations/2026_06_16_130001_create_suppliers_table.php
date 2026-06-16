<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier');
            $table->string('pic')->nullable();       // Nama penanggung jawab/kontak
            $table->string('wa')->nullable();         // Nomor WhatsApp
            $table->text('alamat')->nullable();       // Alamat lengkap
            $table->boolean('status')->default(true); // true = aktif, false = nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
