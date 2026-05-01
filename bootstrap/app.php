<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/pesapal/ipn',
            'pesapal/ipn',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        $middleware->alias([
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'admin.panel' => \App\Http\Middleware\EnsureAdminPanelAccess::class,
            'staff.panel' => \App\Http\Middleware\EnsureStaffPanelAccess::class,
            'active.account' => \App\Http\Middleware\EnsureAccountIsActive::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('bookings:expire-pending')->everyMinute();
        $schedule->command('bookings:notify-stay-ended')->everyFiveMinutes();
    })
    ->create();
