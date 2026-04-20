<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->unsignedInteger('booking_payment_timeout_minutes')->default(30);
            $table->unsignedSmallInteger('stat_pools_count')->nullable();
            $table->unsignedSmallInteger('stat_restaurants_count')->nullable();
            $table->string('home_stat_customers_label', 32)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_payment_timeout_minutes',
                'stat_pools_count',
                'stat_restaurants_count',
                'home_stat_customers_label',
            ]);
        });
    }
};
