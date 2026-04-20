@extends('layouts.reception')

@section('title', __('Edit hotel service'))

@section('content')
    <h1 class="text-30" style="margin:0 0 1rem;">{{ __('Edit hotel service') }}</h1>
    <form method="POST" action="{{ route('reception.hotel-services.update', $service) }}" style="max-width:560px;" data-autosave-key="reception-service-edit-{{ $service->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('reception.hotel-services._form', ['service' => $service, 'branches' => $branches])
        <button type="submit" class="dash-btn dash-btn--primary mt-15">{{ __('Update') }}</button>
        <a href="{{ route('reception.hotel-services.index') }}" class="dash-btn dash-btn--ghost mt-15" style="text-decoration:none;margin-left:.5rem;">{{ __('Cancel') }}</a>
    </form>
@endsection
