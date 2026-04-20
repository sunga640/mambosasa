<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_branches', function (Blueprint $table) {
            $table->text('location_address')->nullable()->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->date('check_in')->nullable()->change();
            $table->date('check_out')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hotel_branches', function (Blueprint $table) {
            $table->text('location_address')->nullable(false)->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->date('check_in')->nullable(false)->change();
            $table->date('check_out')->nullable(false)->change();
        });
    }
};
