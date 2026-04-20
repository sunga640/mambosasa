<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('public_reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_method_id')->constrained('booking_methods')->restrictOnDelete();
            $table->string('status', 32);
            $table->timestamp('payment_deadline_at')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 64);
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('rooms_count')->default(1);
            $table->unsignedInteger('nights')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->boolean('terms_accepted')->default(false);
            $table->text('special_requests')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'status']);
            $table->index(['status', 'payment_deadline_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
