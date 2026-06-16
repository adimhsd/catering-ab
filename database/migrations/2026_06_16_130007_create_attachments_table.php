<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')
                  ->constrained('purchases')
                  ->cascadeOnDelete(); // Hapus lampiran jika transaksi dihapus
            $table->string('file_nota'); // Path file di storage Laravel
            $table->string('original_name')->nullable(); // Nama file asli
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
