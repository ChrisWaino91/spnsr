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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json('products');
            $table->decimal('amount_paid', 10, 2);
            $table->string('customer_email')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('town_city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('third_party_order_reference')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
