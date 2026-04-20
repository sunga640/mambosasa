<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-account-primary']) }}>
    {{ $slot }}
</button>
