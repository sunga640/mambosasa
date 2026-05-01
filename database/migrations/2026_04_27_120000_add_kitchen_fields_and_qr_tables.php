<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->string('request_source', 32)->default('portal')->after('hotel_branch_id');
            $table->string('guest_name')->nullable()->after('request_source');
            $table->string('guest_phone', 40)->nullable()->after('guest_name');
        });

        Schema::create('kitchen_room_qrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_branch_id')->constrained('hotel_branches')->cascadeOnDelete();
            $table->string('token', 80)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();

            $table->unique('room_id');
            $table->index(['hotel_branch_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_room_qrs');

        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->dropColumn(['request_source', 'guest_name', 'guest_phone']);
        });
    }
};
