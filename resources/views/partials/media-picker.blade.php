@php
    $pickerAssets = $assets ?? collect();
    $pickerName = $name ?? 'media_asset_id';
    $pickerLabel = $label ?? __('Select from system gallery');
    $pickerMultiple = (bool) ($multiple ?? false);
    $pickerSelected = $selected ?? ($pickerMultiple ? [] : null);
    $pickerSelectedIds = array_map('strval', (array) $pickerSelected);
@endphp

<div class="form-row">
    <label>{{ $pickerLabel }}</label>
    @if (! $pickerAssets->count())
        <p class="text-13 mt-5" style="opacity:.7;">{{ __('No media found yet. Upload images in the media library first, then come back here to select them.') }}</p>
    @endif
    <details class="media-picker-shell">
        <summary class="media-picker-summary">
            {{ __('Open system media library') }} ({{ $pickerAssets->count() }})
        </summary>
        <div class="media-picker-grid">
            @if($pickerMultiple)
                <input type="hidden" name="{{ $pickerName }}[]" value="">
            @endif
            @unless($pickerMultiple)
                <label class="media-picker-none">
                    <input type="radio" name="{{ $pickerName }}" value="" @checked(($pickerSelectedIds[0] ?? '') === '') style="margin-bottom:6px;">
                    <span class="text-13">{{ __('None') }}</span>
                </label>
            @endunless
            @foreach ($pickerAssets as $asset)
                @php
                    $assetUrl = \App\Support\PublicDisk::url($asset->path);
                    $checked = in_array((string) $asset->id, $pickerSelectedIds, true);
                @endphp
                <label class="media-picker-option{{ $checked ? ' is-selected' : '' }}">
                    <input
                        type="{{ $pickerMultiple ? 'checkbox' : 'radio' }}"
                        name="{{ $pickerMultiple ? $pickerName.'[]' : $pickerName }}"
                        value="{{ $asset->id }}"
                        @checked($checked)
                        style="margin-bottom:6px;"
                    >
                    <img src="{{ $assetUrl }}" alt="{{ $asset->original_name }}" loading="lazy" class="media-picker-thumb">
                    <div class="text-12 mt-5" style="word-break:break-word;">{{ $asset->original_name }}</div>
                </label>
            @endforeach
        </div>
        <div class="text-12 mt-10" style="opacity:.68;">
            {{ __('All uploaded media available to this form is shown here. Open the full media library if you want to manage files separately.') }}
        </div>
    </details>
</div>
