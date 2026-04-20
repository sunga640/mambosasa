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

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1rem;margin-top:1.5rem;">
        @foreach ($assets as $asset)
            <article style="border:1px solid #e5e7eb;border-radius:10px;padding:.65rem;">
                @if (str_starts_with((string) $asset->mime_type, 'video/'))
                    <video controls style="width:100%;height:110px;object-fit:cover;border-radius:8px;" src="{{ \App\Support\PublicDisk::url($asset->path) }}"></video>
                @else
                    <img src="{{ \App\Support\PublicDisk::url($asset->path) }}" alt="" style="width:100%;height:110px;object-fit:cover;border-radius:8px;">
                @endif
                <div class="text-12 mt-5" style="word-break:break-all;">{{ $asset->original_name }}</div>
            </article>
        @endforeach
    </div>

    <div class="mt-20">{{ $assets->links() }}</div>
@endsection
