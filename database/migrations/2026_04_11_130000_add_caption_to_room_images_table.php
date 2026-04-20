<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_images', function (Blueprint $table) {
            $table->string('caption', 160)->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('room_images', function (Blueprint $table) {
            $table->dropColumn('caption');
        });
    }
};
