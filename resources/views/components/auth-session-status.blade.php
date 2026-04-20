@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'account-status-msg']) }}>
        {{ $status }}
    </div>
@endif
