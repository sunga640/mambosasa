@extends('layouts.admin')

@section('title', __('Our properties'))

@section('content')
    <div style="display:flex;align-items:center;gap:1rem;">
        <div style="flex:1;min-width:0;">
            <h1 class="text-30">{{ __('Our properties') }}</h1>
            <p class="text-15 mt-10" style="opacity:.85;max-width:40rem;line-height:1.5;">
                {{ __('Properties directory with quick actions and branch details.') }}
            </p>
        </div>
        <a href="{{ route('admin.branches.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="margin-left:auto;text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;white-space:nowrap;">
            {{ __('Create property/branch') }}
        </a>
    </div>

    <div class="mt-30">
        @include('partials.properties-directory-cards', ['branches' => $branches])
    </div>
@endsection
