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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_courier_name')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('shipping_etd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_cost',
                'shipping_courier',
                'shipping_courier_name',
                'shipping_service',
                'shipping_etd',
            ]);
        });
    }
};
