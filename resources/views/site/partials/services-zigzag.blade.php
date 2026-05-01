@php
    $serviceItems = collect($services ?? [])->values();
@endphp

@if($serviceItems->isNotEmpty())
<section class="site-services-zigzag {{ $sectionClass ?? '' }}">
    <div class="container">
        <div class="site-services-zigzag__head">
            <div>
                <span class="site-kicker">{{ $kicker ?? __('Guest Services') }}</span>
                <h2>{{ $title ?? __('Curated service experiences') }}</h2>
            </div>
            <p>{{ $description ?? __('Discover the thoughtful details, comfort-focused extras, and attentive support that help define a relaxed Mambosasa stay.') }}</p>
        </div>

        <div class="site-services-zigzag__grid">
            @foreach($serviceItems as $svc)
                @php
                    $isLarge = $loop->index % 3 === 0;
                @endphp
                <article class="site-service-zigzag-card {{ $isLarge ? 'site-service-zigzag-card--large' : 'site-service-zigzag-card--small' }}">
                    <div class="site-service-zigzag-card__media" style="background-image:url('{{ $svc->imageUrl() ?: asset('img/about/21/1.png') }}');"></div>
                    <div class="site-service-zigzag-card__overlay">
                        <span class="site-service-zigzag-card__eyebrow">{{ $svc->category ?: __('Service') }}</span>
                        <h3>{{ $svc->name }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($svc->description ?: __('Speak with our team for full service details and availability during your stay.'), $isLarge ? 150 : 95) }}</p>
                        <div class="site-service-zigzag-card__meta">
                            @if((float) $svc->price > 0)
                                <strong>TZS {{ number_format((float) $svc->price, 0) }}</strong>
                            @else
                                <strong>{{ __('Available on request') }}</strong>
                            @endif
                            <a href="{{ route('site.page', ['slug' => 'contact']) }}">{{ __('Read more') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
