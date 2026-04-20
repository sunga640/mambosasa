@extends('layouts.admin')

@section('title', __('Create room type'))

@section('content')
    <h1 class="text-30">{{ __('Create room type') }}</h1>
    <form method="POST" action="{{ route('admin.room-types.store') }}" enctype="multipart/form-data" class="mt-20" data-autosave-key="admin-room-type-create">
        @csrf
        @include('admin.room-types._form', ['roomType' => null, 'branches' => $branches, 'mediaAssets' => $mediaAssets])
        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Save') }}</button>
    </form>
@endsection
