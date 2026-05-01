@php
    $viewImages = $siteSettings->homeViewsGalleryUrls();
@endphp

@if($viewImages !== [])
<section class="site-hotel-views">
    <div class="container">
        <div class="site-hotel-views__head">
            <div>
                <span class="site-kicker">{{ __('Hotel Views') }}</span>
                <h2>{{ __('See the property from every angle') }}</h2>
            </div>
            <p>{{ __('Browse a closer look at our interiors, guest spaces, dining atmosphere, and the quiet details that shape a comfortable Mambosasa stay.') }}</p>
        </div>

        <div class="site-hotel-views__mosaic">
            @foreach(array_slice($viewImages, 0, 5) as $image)
                <figure class="site-hotel-views__tile site-hotel-views__tile--{{ $loop->iteration }}">
                    <img
                        src="{{ $image }}"
                        alt="{{ __('Hotel view :number', ['number' => $loop->iteration]) }}"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        decoding="{{ $loop->first ? 'sync' : 'async' }}">
                    <figcaption class="site-hotel-views__caption">
                        <span>{{ __('Welcome') }}</span>
                        <strong>{{ $siteSettings->company_name ?? config('app.name') }}</strong>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
