<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->foreignId('recipient_user_id')
                ->nullable()
                ->after('room_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['recipient_user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipient_user_id');
        });
    }
};
