<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->foreignId('hotel_branch_id')
                ->nullable()
                ->after('id')
                ->constrained('hotel_branches')
                ->nullOnDelete();
        });

        $fallbackBranchId = DB::table('hotel_branches')
            ->where('is_active', true)
            ->orderBy('name')
            ->value('id');

        if (! $fallbackBranchId) {
            $fallbackBranchId = DB::table('hotel_branches')
                ->orderBy('name')
                ->value('id');
        }

        if ($fallbackBranchId) {
            DB::table('contact_messages')
                ->whereNull('hotel_branch_id')
                ->update(['hotel_branch_id' => $fallbackBranchId]);
        }
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('hotel_branch_id');
        });
    }
};
