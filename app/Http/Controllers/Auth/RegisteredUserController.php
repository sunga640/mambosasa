<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $guestRoleId = Role::query()->where('slug', Role::GUEST_SLUG)->value('id');
        $superAdminRoleId = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->value('id');
        $emailLower = strtolower($request->email);
        $isListedSuperAdmin = in_array($emailLower, config('hotel.super_admin_emails', []), true);
        $roleId = ($isListedSuperAdmin && $superAdminRoleId) ? $superAdminRoleId : $guestRoleId;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'email_verified_at' => $roleId === $guestRoleId ? Carbon::now() : null,
        ]);

        event(new Registered($user));

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Account created successfully. Please login.');
    }
}
