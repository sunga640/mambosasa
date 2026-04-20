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
       Schema::table('booking_methods', function (Blueprint $table) {
    $table->boolean('show_on_booking_page')->default(1);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_methods', function (Blueprint $table) {
            //
        });
    }
};
