@extends('layouts.reception')

@section('title', __('Add hotel service'))

@section('content')
    <h1 class="text-30" style="margin:0 0 1rem;">{{ __('Add hotel service') }}</h1>
    <form method="POST" action="{{ route('reception.hotel-services.store') }}" style="max-width:560px;" data-autosave-key="reception-service-create" enctype="multipart/form-data">
        @csrf
        @include('reception.hotel-services._form', ['service' => null, 'branches' => $branches])
        <button type="submit" class="dash-btn dash-btn--primary mt-15">{{ __('Save') }}</button>
        <a href="{{ route('reception.hotel-services.index') }}" class="dash-btn dash-btn--ghost mt-15" style="text-decoration:none;margin-left:.5rem;">{{ __('Cancel') }}</a>
    </form>
@endsection
