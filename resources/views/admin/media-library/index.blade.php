@extends('layouts.admin')

@section('title', __('System media library'))

@section('content')
    <h1 class="text-30">{{ __('System media library') }}</h1>
    <form method="POST" action="{{ route('admin.media-library.store') }}" enctype="multipart/form-data" class="mt-20" data-autosave-key="admin-media-library-upload">
        @csrf
        <div class="form-row">
            <label for="files">{{ __('Upload media files') }}</label>
            <input id="files" type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.avif,.mp4,.webm,.mov">
        </div>
        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Upload') }}</button>
    </form>

    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;margin-top:1rem;">
        <p class="text-13" style="opacity:.75;margin:0;">{{ __('Media files are lazy-loaded to keep this page responsive while you manage the gallery.') }}</p>
        <div class="text-13" style="opacity:.75;">{{ __('Page') }} {{ $assets->currentPage() }} / {{ $assets->lastPage() }}</div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1rem;margin-top:1.5rem;">
        @foreach ($assets as $asset)
            <article style="border:1px solid rgba(213, 172, 66, 0.18);border-radius:0;padding:.65rem;background:var(--brand-theme-surface, #2e333b);">
                @if (str_starts_with((string) $asset->mime_type, 'video/'))
                    <video controls preload="metadata" style="width:100%;height:110px;object-fit:cover;border-radius:0;" src="{{ \App\Support\PublicDisk::url($asset->path) }}"></video>
                @else
                    <img src="{{ \App\Support\PublicDisk::url($asset->path) }}" alt="" loading="lazy" style="width:100%;height:110px;object-fit:cover;border-radius:0;">
                @endif
                <div class="text-12 mt-5" style="word-break:break-all;">{{ $asset->original_name }}</div>
                <form method="POST" action="{{ route('admin.media-library.destroy', $asset) }}" class="mt-10" onsubmit="return confirm(@json(__('Delete this media file?')));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dash-btn dash-btn--ghost" style="width:100%;color:#b91c1c;border-color:#fecaca;">{{ __('Delete') }}</button>
                </form>
            </article>
        @endforeach
    </div>

    <div class="mt-20">{{ $assets->links() }}</div>
@endsection
