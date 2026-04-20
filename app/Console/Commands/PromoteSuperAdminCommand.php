<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteSuperAdminCommand extends Command
{
    protected $signature = 'hotel:promote-super-admin
                            {email? : Barua pepe ya mtumiaji (ikiwa hutoi, utachagua kutoka orodha)}
                            {--dry-run : Onyesha tu bila kubadilisha database}';

    protected $description = 'Mpa mtumiaji jukumu la super admin (kupitia barua pepe au uchaguzi wa mwingiliano)';

    public function handle(): int
    {
        $role = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->first();

        if (! $role) {
            $this->error('Jukumu la super admin halipo. Endesha: php artisan db:seed');

            return self::FAILURE;
        }

        $email = $this->argument('email');

        if ($email === null || $email === '') {
            if ($this->option('no-interaction')) {
                $this->error('Taja barua pepe (hauwezi kutumia --no-interaction bila email): hotel:promote-super-admin user@example.com');

                return self::FAILURE;
            }

            $users = User::query()->orderBy('id')->get(['id', 'name', 'email']);

            if ($users->isEmpty()) {
                $this->error('Hakuna watumiaji kwenye mfumo.');

                return self::FAILURE;
            }

            if ($users->count() === 1) {
                $email = $users->first()->email;
                $this->info("Mtumiaji mmoja tu: {$email}");
            } else {
                $choices = $users->mapWithKeys(fn (User $u) => [
                    $u->email => "{$u->name} <{$u->email}>",
                ])->all();

                $email = $this->choice('Chagua mtumiaji wa kumpa super admin', $choices);
            }
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
            ->first();

        if (! $user) {
            $this->error("Hakuna mtumiaji aliye na barua pepe: {$email}");

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->warn("[dry-run] Ningempa super admin: {$user->email} ({$user->name})");

            return self::SUCCESS;
        }

        $user->update(['role_id' => $role->id]);

        $this->info("Super admin imewekwa kwa: {$user->email} ({$user->name}). Toka na uingie tena kwenye browser ili session ipya.");

        return self::SUCCESS;
    }
}
