<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('b2b_price', 15, 2)->nullable()->after('price');
            $table->integer('minimum_order_quantity')->default(1)->after('b2b_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['b2b_price', 'minimum_order_quantity']);
        });
    }
};
