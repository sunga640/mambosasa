<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $guestRoleId = Role::query()->where('slug', Role::GUEST_SLUG)->value('id');
        if (! $guestRoleId) {
            return;
        }

        User::query()
            ->where('role_id', $guestRoleId)
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    if (filled($user->system_password_plain) && $user->uses_system_password) {
                        continue;
                    }
                    $plain = Str::password(12);
                    $user->forceFill([
                        'password' => $plain,
                        'uses_system_password' => true,
                        'system_password_plain' => $plain,
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        // no-op
    }
};
