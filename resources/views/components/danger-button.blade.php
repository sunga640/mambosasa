<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-account-danger']) }}>
    {{ $slot }}
</button>
