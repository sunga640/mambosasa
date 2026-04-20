@props(['value'])

<label {{ $attributes->merge(['class' => 'account-label']) }}>
    {{ $value ?? $slot }}
</label>
