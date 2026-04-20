<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->timestamp('checkout_notified_at')->nullable()->after('confirmed_at');
        });

        Schema::create('dashboard_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 64);
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['read_at', 'created_at']);
            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_notifications');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['confirmed_at', 'checkout_notified_at']);
        });
    }
};
