@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'account-input-error']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
