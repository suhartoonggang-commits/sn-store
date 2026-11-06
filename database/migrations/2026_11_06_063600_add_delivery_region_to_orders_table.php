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
            $table->char('province_code', 2)->nullable();
            $table->string('province_name')->nullable();
            $table->char('city_code', 4)->nullable();
            $table->string('city_name')->nullable();
            $table->char('district_code', 7)->nullable();
            $table->string('district_name')->nullable();
            $table->char('village_code', 10)->nullable();
            $table->string('village_name')->nullable();
            $table->string('postal_code', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'province_code',
                'province_name',
                'city_code',
                'city_name',
                'district_code',
                'district_name',
                'village_code',
                'village_name',
                'postal_code',
            ]);
        });
    }
};
