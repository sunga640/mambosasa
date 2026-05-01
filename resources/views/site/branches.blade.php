@extends('layouts.site')

@section('title', __('Our properties'))

@section('content')
    <section data-anim-wrap class="pageHero -type-1 -items-center">
        <div class="pageHero__bg">
            @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/4.png', 'heroUrl' => $heroUrl ?? null])
        </div>
        <div class="container">
            <div class="row justify-center">
                <div class="col-auto">
                    <div data-split="lines" data-anim-child="split-lines delay-3" class="pageHero__content text-center">
                        <h1 class="pageHero__title text-white">{{ __('Our properties') }}</h1>
                        <p class="pageHero__text text-white">{{ __('Explore the Mambosasa locations welcoming guests with comfortable stays, attentive service, and convenient access to key city destinations.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="layout-pt-lg layout-pb-lg">
        <div class="container">
            @include('partials.properties-directory-cards', ['branches' => $branches, 'readOnly' => true])
        </div>
    </section>
@endsection
