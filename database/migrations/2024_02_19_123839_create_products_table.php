<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('api_parent_id');
            $table->integer('api_id');
            $table->foreignId('brand_id');
            $table->unsignedInteger('supplier_id');
            $table->string('title');
            $table->string('reference');
            $table->decimal('price', 8, 3);
            $table->decimal('sale_price', 8, 3);
            $table->decimal('rr_price', 8, 3);
            $table->integer('stock');
            $table->json('images');
            $table->foreignId('category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
