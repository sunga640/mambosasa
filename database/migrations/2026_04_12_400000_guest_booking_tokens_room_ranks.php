<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('room_rank_id')->nullable()->after('hotel_branch_id')->constrained('room_ranks')->nullOnDelete();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('guest_access_token', 64)->nullable()->unique()->after('public_reference');
            $table->timestamp('guest_access_token_expires_at')->nullable()->after('guest_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_access_token', 'guest_access_token_expires_at']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('room_rank_id');
        });

        Schema::dropIfExists('room_ranks');
    }
};
