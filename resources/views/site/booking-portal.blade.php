<x-guest-layout :title="__('Your account')">
    <p class="text-15" style="margin:0 0 1rem;text-align:center;opacity:.85;">
        {{ __('Booking :ref — enter your full name exactly as on the reservation.', ['ref' => $booking->public_reference]) }}
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('site.booking.portal.login') }}">
        @csrf
        <input type="hidden" name="public_reference" value="{{ $booking->public_reference }}">

        <div class="account-field-stack">
            <x-input-label for="full_name" :value="__('Full name')" />
            <x-text-input
                id="full_name"
                type="text"
                name="full_name"
                :value="old('full_name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('full_name')" />
        </div>

        <div class="account-field-stack" style="margin-top:1rem;">
            <label class="account-label" for="portal_password_display">{{ __('Password (auto-generated)') }}</label>
            <input
                id="portal_password_display"
                type="password"
                class="account-field-input"
                value="{{ $plainPassword }}"
                readonly
                autocomplete="new-password"
                aria-readonly="true"
                style="opacity:.92;"
            />
            <p class="text-12" style="margin:.35rem 0 0;opacity:.75;">{{ __('Shown for your reference. Sign-in is completed using your full name above.') }}</p>
        </div>

        <div class="auth-actions-row" style="margin-top:1.5rem;">
            <x-primary-button>{{ __('Go to member dashboard') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
