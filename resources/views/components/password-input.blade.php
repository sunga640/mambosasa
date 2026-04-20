@props(['label' => __('Password')])

@php
    $fieldId = $attributes->get('id')
        ?? str_replace(['[', ']', "'", '"'], '_', (string) $attributes->get('name', 'password'));
@endphp

<div class="account-field-stack">
    <x-input-label :for="$fieldId" :value="$label" />
    <div class="auth-password-wrap">
        <x-text-input {{ $attributes->merge(['id' => $fieldId, 'type' => 'password']) }} />
        <button
            type="button"
            class="auth-password-toggle js-password-toggle"
            aria-label="{{ __('Show password') }}"
            aria-pressed="false"
        >
            <svg class="js-eye-on" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <svg class="js-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
        </button>
    </div>
</div>
