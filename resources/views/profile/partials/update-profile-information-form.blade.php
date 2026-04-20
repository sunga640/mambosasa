<section class="profile-section">
    <header>
        <h2>
            {{ __('Profile Information') }}
        </h2>

        <p>
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="account-field-stack">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="account-field-stack account-field-stack--mt">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="account-muted" style="margin-top:0.75rem;margin-bottom:0;">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" type="submit" class="account-link" style="background:none;border:none;padding:0;cursor:pointer;font:inherit;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="account-status-msg" style="margin-top:0.5rem;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions-row">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="saved-hint">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
