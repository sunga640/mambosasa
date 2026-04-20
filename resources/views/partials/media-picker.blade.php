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
    <details style="border:1px solid #d9d9d9;border-radius:10px;background:#fafafa;padding:8px 10px;">
        <summary style="cursor:pointer;user-select:none;font-weight:500;">
            {{ __('Open system media library') }} ({{ $pickerAssets->count() }})
        </summary>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px;max-height:320px;overflow:auto;padding:10px 0 0;">
            @unless($pickerMultiple)
                <label style="display:block;cursor:pointer;">
                    <input type="radio" name="{{ $pickerName }}" value="" @checked(($pickerSelectedIds[0] ?? '') === '') style="margin-bottom:6px;">
                    <span class="text-13">{{ __('None') }}</span>
                </label>
            @endunless
            @foreach ($pickerAssets as $asset)
                @php
                    $assetUrl = \App\Support\PublicDisk::url($asset->path);
                    $checked = in_array((string) $asset->id, $pickerSelectedIds, true);
                @endphp
                <label style="border:1px solid {{ $checked ? '#111' : '#d0d0d0' }};border-radius:10px;padding:8px;background:#fff;cursor:pointer;">
                    <input
                        type="{{ $pickerMultiple ? 'checkbox' : 'radio' }}"
                        name="{{ $pickerMultiple ? $pickerName.'[]' : $pickerName }}"
                        value="{{ $asset->id }}"
                        @checked($checked)
                        style="margin-bottom:6px;"
                    >
                    <img src="{{ $assetUrl }}" alt="{{ $asset->original_name }}" style="display:block;width:100%;height:88px;object-fit:cover;border-radius:7px;border:1px solid #eee;">
                    <div class="text-12 mt-5" style="word-break:break-word;">{{ $asset->original_name }}</div>
                </label>
            @endforeach
        </div>
    </details>
</div>
