<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function lookup(Request $request)
    {
        $login = trim((string) $request->input('login', ''));
        if ($login === '') {
            return response()->json(['found' => false]);
        }

        $user = User::findForLogin($login);

        if (! $user) {
            return response()->json(['found' => false]);
        }

        $isGuest = $user->role?->slug === Role::GUEST_SLUG;
        if ($isGuest && $user->uses_system_password && blank($user->system_password_plain)) {
            $plain = Str::password(12);
            $user->forceFill([
                'password' => $plain,
                'system_password_plain' => $plain,
                'uses_system_password' => true,
            ])->save();
            $user->refresh();
        }
        $auto = $isGuest && $user->uses_system_password && filled($user->system_password_plain);

        return response()->json([
            'found' => true,
            'is_guest' => $isGuest,
            'auto_password' => $auto ? $user->system_password_plain : null,
            'disable_password' => $auto,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->session()->forget(['site_booking_portal_booking_id', 'booking_portal_ref']);

        $user = auth()->user();
        $default = route('dashboard', absolute: false);
        if ($user?->isSuperAdmin()) {
            $default = route('admin.dashboard', absolute: false);
        } elseif ($user?->isReceptionStaff()) {
            $default = route('reception.dashboard', absolute: false);
        }

        $user = auth()->user();

        return redirect()->to($user->accountHomeUrl());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
