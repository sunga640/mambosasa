@extends('layouts.site')

@section('title', __('Search'))

@section('content')
    <section data-anim-wrap class="pageHero -type-1 -items-center">
        <div class="pageHero__bg">
            @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/4.png', 'heroUrl' => $heroUrl ?? null])
        </div>
        <div class="container">
            <div class="row justify-center">
                <div class="col-auto">
                    <div data-split="lines" data-anim-child="split-lines delay-3" class="pageHero__content text-center">
                        <h1 class="pageHero__title text-white">{{ __('Search rooms') }}</h1>
                        <p class="pageHero__text text-white">{{ __('Find a room by name, number, or description at :name.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="layout-pt-lg layout-pb-lg">
        <div class="container">
            <div class="row y-gap-25 justify-between items-end">
                <div class="col-lg-7">
                    <h2 class="text-30 md:text-24" style="margin:0;">{{ __('Results') }}</h2>
                    @if (strlen($q) >= 2)
                        <p class="text-15 mt-10" style="opacity:.85;">
                            {{ __('Showing matches for') }} <strong class="text-dark-1">“{{ $q }}”</strong>
                            @if ($rooms->total() > 0)
                                — {{ trans_choice(':count room found|:count rooms found', $rooms->total(), ['count' => $rooms->total()]) }}
                            @endif
                        </p>
                    @endif
                </div>
                <div class="col-lg-5">
                    <form method="GET" action="{{ route('site.search') }}" class="mt-10 lg:mt-0">
                        <div class="site-inline-search-pill">
                            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Room type, number…') }}" autocomplete="off" autofocus aria-label="{{ __('Search rooms') }}">
                            <button type="submit" class="site-inline-search-pill__go" aria-label="{{ __('Search') }}">{{ __('Go') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            @if (strlen($q) < 2)
                <p class="text-15 mt-30" style="opacity:.8;">{{ __('Enter at least 2 characters to search.') }}</p>
            @elseif ($rooms->isEmpty())
                <div class="mt-40 p-40 rounded-16 text-center" style="border:1px solid rgba(18,34,35,.1);background:#fafafa;">
                    <p class="text-18 fw-500 mb-5">{{ __('No rooms found') }}</p>
                    <p class="text-14" style="opacity:.75;">{{ __('Try another keyword or browse from the home page.') }}</p>
                    <a href="{{ route('site.home') }}#rooms" class="button -md -outline-dark-1 mt-25">{{ __('View all rooms') }}</a>
                </div>
            @else
                <div class="row y-gap-30 x-gap-30 pt-40 sm:pt-25">
                    @foreach ($rooms as $room)
                        @php
                            $snippet = \Illuminate\Support\Str::limit(trim(strip_tags($room->description ?? '')), 160);
                        @endphp
                        <div class="col-lg-6">
                            <article class="d-flex flex-column flex-md-row rounded-16 overflow-hidden h-full" style="border:1px solid rgba(18,34,35,.12);background:#fff;">
                                <a href="{{ route('site.booking', ['room' => $room->id]) }}" class="d-block flex-shrink-0" style="width:100%;max-width:280px;">
                                    <div class="ratio ratio-1:1 ratio-md-16:9 h-full">
                                        @if ($room->usesVideoOnCard() && $room->videoPublicUrl())
                                            <video class="img-ratio" style="width:100%;height:100%;object-fit:cover;" src="{{ $room->videoPublicUrl() }}" poster="{{ $room->heroImagePublicUrl() ?? $room->images->first()?->url() }}" muted playsinline loop autoplay></video>
                                        @else
                                            <img src="{{ $room->cardImageUrl() }}" alt="{{ $room->name }}" class="img-ratio">
                                        @endif
                                    </div>
                                </a>
                                <div class="px-25 py-25 d-flex flex-column flex-grow-1">
                                    <div class="text-12 uppercase" style="opacity:.6;letter-spacing:.04em;">{{ $room->branch?->name ?? __('Hotel') }}</div>
                                    @if ($room->branch && ($room->branch->city || $room->branch->location_address))
                                        <div class="text-13 mt-5" style="opacity:.65;">{{ \Illuminate\Support\Str::limit(trim(implode(' · ', array_filter([$room->branch->location_address, $room->branch->city, $room->branch->country]))), 100) }}</div>
                                    @endif
                                    <h3 class="text-22 fw-600 mt-10 mb-5">
                                        <a href="{{ route('site.booking', ['room' => $room->id]) }}" class="text-dark-1">{{ $room->name }}</a>
                                    </h3>
                                    <div class="d-flex flex-wrap x-gap-15 y-gap-5 text-14" style="opacity:.8;">
                                        @if ($room->room_number)
                                            <span>{{ __('Room #:n', ['n' => $room->room_number]) }}</span>
                                        @endif
                                        <span>{{ __('Floor :f', ['f' => $room->floor_number]) }}</span>
                                        <span>{{ $room->status->label() }}</span>
                                    </div>
                                    @if ($snippet !== '')
                                        <p class="text-14 mt-15 lh-16 flex-grow-1" style="opacity:.82;">{{ $snippet }}</p>
                                    @endif
                                    <div class="d-flex items-center justify-between flex-wrap gap-3 mt-20 pt-15" style="border-top:1px solid rgba(18,34,35,.08);">
                                        <div class="text-20 fw-600">{{ __(':amount / night', ['amount' => number_format((float) $room->price, 0)]) }}</div>
                                        <div class="d-flex x-gap-10 flex-wrap">
                                            <a href="{{ route('site.booking', ['room' => $room->id]) }}" class="btn-site-book">{{ __('Book now') }}</a>
                                            <form method="post" action="{{ route('site.cart.add') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                                <input type="hidden" name="qty" value="1">
                                                <button type="submit" class="btn-site-cart">{{ __('Add to cart') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                @if ($rooms->hasPages())
                    <div class="d-flex justify-center pt-50">
                        {{ $rooms->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
