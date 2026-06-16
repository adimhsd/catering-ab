<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // Format: PB-YYYYMMDD-XXX (auto-generate via PurchaseService)
            $table->string('nomor_transaksi')->unique();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete(); // Admin dapur yang menginput
            $table->date('tanggal');
            // Total dihitung dari penjumlahan subtotal semua detail item
            $table->decimal('total', 15, 2)->default(0);
            $table->text('catatan')->nullable(); // Catatan opsional per transaksi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
