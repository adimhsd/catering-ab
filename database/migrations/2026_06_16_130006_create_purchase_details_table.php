<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')
                  ->constrained('purchases')
                  ->cascadeOnDelete(); // Hapus detail jika header dihapus
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();
            $table->decimal('qty', 10, 2);
            $table->decimal('harga', 15, 2);       // Harga satuan
            $table->decimal('subtotal', 15, 2);    // qty × harga (dihitung di PHP)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
