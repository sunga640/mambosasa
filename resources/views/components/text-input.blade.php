@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'account-field-input']) }}>
