<x-guest-layout :title="__('Confirm password')">
    <p class="account-muted">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <x-password-input id="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" />

        <div class="account-form-footer">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
