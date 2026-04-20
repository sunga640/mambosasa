@extends('layouts.member')

@section('title', __('Our properties'))

@section('content')
    <h1 class="text-30">{{ __('Our properties') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;max-width:40rem;line-height:1.5;">
        {{ __('Branches and contacts from your hotel system. Guests can also open the public page.') }}
        <a href="{{ route('site.branches') }}" class="text-dark-1 fw-600" style="text-decoration:underline;">{{ __('Open public page') }}</a>
    </p>

    <div class="mt-30">
        @include('partials.properties-directory-cards', ['branches' => $branches])
    </div>
@endsection
