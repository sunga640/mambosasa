<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_branch_id')->nullable()->constrained('hotel_branches')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('category', 64)->default('general');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('booking_hotel_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('hotel_service_id')->constrained('hotel_services')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status', 32)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_hotel_service_requests');
        Schema::dropIfExists('hotel_services');
    }
};
