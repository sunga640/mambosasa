<?php

namespace App\Providers;

use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Eloquent\AdminUserRepository;
use App\Repositories\Eloquent\PermissionRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Models\DashboardNotification;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomServiceOrder;
use App\Models\SystemSetting;
use App\View\Composers\SiteSettingsComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
            $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
            $this->app->bind(AdminUserRepositoryInterface::class, AdminUserRepository::class);
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            Route::model('branch', HotelBranch::class);
            Route::model('room', Room::class);
            Route::model('roomServiceOrder', RoomServiceOrder::class);
            Route::model('notification', DashboardNotification::class);

            Paginator::useBootstrapFive();

            View::composer(
                ['layouts.site', 'layouts.guest', 'site.partials.*', 'site.home', 'site.*'],
                SiteSettingsComposer::class
            );

            $settings = SystemSetting::current();
            view()->share('dashboardSettings', $settings);

            // Allow runtime SMTP setup from admin system settings.
            if ($settings->smtp_host && $settings->smtp_port) {
                $smtpConfig = [
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $settings->smtp_host,
                    'mail.mailers.smtp.port' => (int) $settings->smtp_port,
                    'mail.mailers.smtp.username' => $settings->smtp_username,
                    'mail.mailers.smtp.password' => $settings->smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->smtp_encryption ?: null,
                    'mail.from.address' => $settings->mail_from_address ?: config('mail.from.address'),
                    'mail.from.name' => $settings->mail_from_name ?: config('mail.from.name'),
                ];

                $opensslCaFile = (string) ini_get('openssl.cafile');
                if ($opensslCaFile !== '' && ! is_file($opensslCaFile)) {
                    // Fallback for Windows/XAMPP setups where PHP points to a removed CA bundle path.
                    $smtpConfig['mail.mailers.smtp.stream'] = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ];
                }

                config($smtpConfig);
            }
        }
    }
