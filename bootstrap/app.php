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
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        $middleware->alias([
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'staff.panel' => \App\Http\Middleware\EnsureStaffPanelAccess::class,
            'active.account' => \App\Http\Middleware\EnsureAccountIsActive::class,
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware): void {
    // HAPA: Ruhusu PesaPal kupita bila CSRF token
    $middleware->validateCsrfTokens(except: [
        'api/pesapal/ipn',  // Hii lazima ifanane na URL uliyoweka kwenye routes
        'pesapal/ipn'       // Ongeza zote mbili kwa usalama
    ]);

    $middleware->web(append: [
        \App\Http\Middleware\PreventBackHistory::class,
    ]);

    $middleware->alias([
        'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
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

