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
        Schema::create('sub_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('title');
            $table->string('reference');
            $table->integer('stock');
            $table->boolean('active')->default(true);
            $table->boolean('deleted')->default(false);
            $table->decimal('price', 8, 3);
            $table->decimal('sale_price', 8, 3);
            $table->decimal('rrp_price', 8, 3);
            $table->json('images');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_products');
    }
};
