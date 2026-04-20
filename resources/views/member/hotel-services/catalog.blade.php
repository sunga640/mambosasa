@extends('layouts.member')

@section('title', __('Hotel services'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('Hotel services') }}</h1>
@endsection

@section('content')
    <p class="text-15" style="line-height:1.6;margin-bottom:1.5rem;max-width:42rem;">
        {{ __('Add-on services you can request after booking — same categories as we use at the property. Prices are indicative in TZS; staff will confirm details.') }}
    </p>
    @if ($filterBranchId)
        <p class="text-13 mb-20" style="opacity:.8;">{{ __('Filtered using your branch selection in the header.') }}</p>
    @endif

    @if ($groupedServices->isEmpty())
        <p class="text-15">{{ __('No services are published yet.') }}</p>
    @else
        <div class="row y-gap-40 x-gap-30" style="margin:0 -.5rem;">
            @foreach ($groupedServices as $category => $items)
                <div class="col-12" style="padding:0 .5rem;">
                    <article style="border:1px solid #e8e8e8;border-radius:16px;overflow:hidden;background:#fff;">
                        <div style="padding:1.25rem 1.5rem;background:linear-gradient(135deg,#fafafa 0%,#fff 100%);border-bottom:1px solid #eee;">
                            <p class="text-12" style="text-transform:uppercase;letter-spacing:.1em;color:#64748b;font-weight:600;margin:0;">{{ __('Category') }}</p>
                            <h2 class="text-22" style="margin:.35rem 0 0;">{{ ucfirst($category) }}</h2>
                        </div>
                        <div style="padding:1.25rem 1.5rem 1.5rem;">
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;">
                                @foreach ($items as $svc)
                                    <article class="site-pricing-card" style="border:1px solid #eee;border-radius:12px;overflow:hidden;">
                                        <div class="site-pricing-card__body" style="padding:1rem 1.1rem;">
                                            <h3 class="site-pricing-card__title" style="margin:0 0 .5rem;font-size:1.1rem;">{{ $svc->name }}</h3>
                                            @if ($svc->description)
                                                <p class="site-pricing-card__excerpt" style="margin:0 0 .75rem;">{{ \Illuminate\Support\Str::limit(strip_tags($svc->description), 140) }}</p>
                                            @endif
                                            @if ($svc->branch)
                                                <p class="text-13" style="opacity:.75;margin:0 0 .5rem;">{{ $svc->branch->name }}</p>
                                            @endif
                                            <div class="site-pricing-card__price-row">
                                                <span class="site-pricing-card__amount">{{ number_format((float) $svc->price, 0) }}</span>
                                                <span class="site-pricing-card__per">{{ __('TZS') }}</span>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif

    <p class="mt-30"><a href="{{ route('bookings.index') }}">{{ __('Open a booking') }}</a> {{ __('to request a service for that stay.') }}</p>
@endsection
