<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('booking_methods', function (Blueprint $table) {
        $table->string('gateway_ipn_id')->nullable()->after('gateway_base_url');
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
