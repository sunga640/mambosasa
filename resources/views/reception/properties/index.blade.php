@extends('layouts.reception')

@section('title', __('Our properties'))

@section('content')
    <h1 class="text-30">{{ __('Our properties') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;max-width:40rem;line-height:1.5;">
        {{ __('Overview of active branches. Manage details under Admin → Branches.') }}
        <a href="{{ route('site.branches') }}" class="text-dark-1 fw-600" style="text-decoration:underline;">{{ __('Public page') }}</a>
    </p>

    <div class="mt-30">
        @include('partials.properties-directory-cards', ['branches' => $branches, 'readOnly' => auth()->user()?->role?->slug === \App\Models\Role::RECEPTION_SLUG])
    </div>
@endsection
