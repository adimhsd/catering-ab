<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->foreignId('category_id')
                  ->constrained('product_categories')
                  ->restrictOnDelete();
            $table->foreignId('unit_id')
                  ->constrained('units')
                  ->restrictOnDelete();
            $table->boolean('status')->default(true); // aktif / nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
