<section class="profile-section">
    <header>
        <h2>
            {{ __('Update Password') }}
        </h2>

        <p>
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <x-password-input
            id="update_password_current_password"
            name="current_password"
            :label="__('Current Password')"
            autocomplete="current-password"
        />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" />

        <div class="account-field-stack--mt">
            <x-password-input
                id="update_password_password"
                name="password"
                :label="__('New Password')"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="account-field-stack--mt">
            <x-password-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                :label="__('Confirm Password')"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions-row">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="saved-hint">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
