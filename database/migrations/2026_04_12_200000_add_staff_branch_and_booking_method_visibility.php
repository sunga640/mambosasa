<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('hotel_branch_id')->nullable()->after('role_id')->constrained('hotel_branches')->nullOnDelete();
        });

        Schema::table('booking_methods', function (Blueprint $table) {
            $table->string('visibility', 16)->default('public')->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('booking_methods', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_branch_id');
        });
    }
};
