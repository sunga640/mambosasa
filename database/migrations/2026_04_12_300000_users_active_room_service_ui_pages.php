<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('system_settings', function (Blueprint $table) {
            $table->json('ui_page_heroes')->nullable()->after('inner_page_hero_image_url');
        });

        Schema::create('restaurant_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('preparation_minutes')->default(25);
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('room_service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_branch_id')->constrained('hotel_branches')->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->timestamp('estimated_ready_at')->nullable();
            $table->unsignedSmallInteger('preparation_minutes')->default(30);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hotel_branch_id', 'status']);
        });

        Schema::create('room_service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_service_order_id')->constrained('room_service_orders')->cascadeOnDelete();
            $table->foreignId('restaurant_menu_item_id')->nullable()->constrained('restaurant_menu_items')->nullOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_service_order_items');
        Schema::dropIfExists('room_service_orders');
        Schema::dropIfExists('restaurant_menu_items');

        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('ui_page_heroes');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
