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
        Schema::table('product_promotion', function (Blueprint $table) {
            if (!Schema::hasColumn('product_promotion', 'id')) {
                $table->id()->first();
            }
            if (!Schema::hasColumn('product_promotion', 'active')) {
                $table->boolean('active')->default(true)->after('promotion_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_promotion', function (Blueprint $table) {
            if (Schema::hasColumn('product_promotion', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};
