@extends('layouts.admin')

@section('title', __('Edit room type'))

@section('content')
    <h1 class="text-30">{{ __('Edit room type') }}</h1>
    <form method="POST" action="{{ route('admin.room-types.update', $roomType) }}" enctype="multipart/form-data" class="mt-20" data-autosave-key="admin-room-type-edit-{{ $roomType->id }}">
        @csrf
        @method('PUT')
        @include('admin.room-types._form', ['roomType' => $roomType, 'branches' => $branches, 'mediaAssets' => $mediaAssets])
        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Update') }}</button>
    </form>
@endsection
