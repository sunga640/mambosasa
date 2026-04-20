<section class="profile-section">
    <header>
        <h2>
            {{ __('Delete Account') }}
        </h2>

        <p>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" data-swal-delete
              data-swal-title="{{ __('Delete your account permanently?') }}"
              data-swal-text="{{ __('You will be logged out and all data will be removed.') }}">
            @csrf
            @method('delete')

            <h2 style="font-size:1.15rem;font-weight:600;color:#122223;margin:0 0 0.35rem;">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="account-muted" style="margin-bottom:1rem;">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <x-password-input
                id="delete_account_password"
                name="password"
                :label="__('Password')"
                placeholder="{{ __('Password') }}"
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" />

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
