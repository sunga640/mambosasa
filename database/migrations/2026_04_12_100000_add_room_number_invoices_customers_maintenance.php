<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_number', 32)->nullable()->after('hotel_branch_id');
            $table->boolean('force_in_use')->default(false)->after('status');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('last_booking_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('token', 64)->unique();
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 8)->default('USD');
            $table->json('line_items')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->unique('booking_id');
        });

        Schema::create('room_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_branch_id')->constrained('hotel_branches')->cascadeOnDelete();
            $table->string('kind', 64);
            $table->text('description')->nullable();
            $table->decimal('expenses', 12, 2)->default(0);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['hotel_branch_id', 'status']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 120);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('room_maintenances');

        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['room_number', 'force_in_use']);
        });
    }
};
