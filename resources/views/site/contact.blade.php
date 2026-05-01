@extends('layouts.site')

@section('title', __('Contact'))

@section('content')
@php
    $mapQuery = urlencode('Mambosasa Hotel Sinza, Dar es Salaam');
@endphp

<section class="layout-pt-lg layout-pb-lg site-page-top-safe">
    <div class="container">
        @if (session('status'))
            <div class="row justify-center mb-40">
                <div class="col-xl-8 col-lg-10">
                    <div class="p-20 bg-light-1 rounded-8 text-15 text-center" role="status">{{ session('status') }}</div>
                </div>
            </div>
        @endif

        <div class="row justify-center text-center">
            <div class="col-xl-8 col-lg-10">
                <div class="mb-25">
                    <span style="display:inline-flex;align-items:center;gap:12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:3px;color:#2563eb;">
                        <span style="width:40px;height:1px;background:#2563eb;"></span>
                        {{ __('Get in Touch') }}
                    </span>
                </div>
                <h1 style="font-size:clamp(30px, 4.5vw, 52px); font-weight:800; color:#051039; line-height:1.08; letter-spacing:-1.5px; margin-bottom:20px;">
                    {{ __('Your Journey to Absolute Comfort Starts Here') }}
                </h1>
                <p class="lh-17 mt-20">
                    {{ __('Reach the team at') }} {{ $siteSettings->company_name ?? config('app.name') }}. {{ __('We read every message and reply during business hours.') }}
                </p>

                @if ($errors->any())
                    <div class="mt-30 p-20 bg-light-1 rounded-8 text-accent-1 text-15 text-left" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('site.contact.submit') }}" class="contactForm row y-gap-30 pt-50 text-left">
                    @csrf
                    @if (request()->filled('branch_id'))
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                    @endif
                    <div class="col-md-6">
                        <div class="form-input">
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            <label class="lh-1 text-16 text-light-1">{{ __('First Name') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-input">
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            <label class="lh-1 text-16 text-light-1">{{ __('Last Name') }}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-input">
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            <label class="lh-1 text-16 text-light-1">{{ __('Email') }}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-input">
                            <input type="text" name="phone" value="{{ old('phone') }}">
                            <label class="lh-1 text-16 text-light-1">{{ __('Phone Number') }}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-input">
                            <textarea name="body" required class="border-1" rows="8">{{ old('body') }}</textarea>
                            <label class="lh-1">{{ __('Message') }}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="button -md -type-2 w-1/1 bg-accent-2 -accent-1">{{ __('Send your message') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="contact-location-band">
    <div class="container">
        <div class="contact-location-grid">
            <div class="contact-location-map">
                <iframe
                    width="100%"
                    height="100%"
                    frameborder="0"
                    style="border:0;"
                    src="https://maps.google.com/maps?q={{ $mapQuery }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="contact-location-copy">
                <span class="contact-location-copy__eyebrow">{{ __('Visit our location') }}</span>
                <h2>{{ __('Find the hotel quickly and reach the right team without delays.') }}</h2>
                <p>{{ __('Use the details below for directions, reservations support, special requests, or any assistance before and during your stay.') }}</p>

                <div class="contact-location-copy__stack">
                    <article>
                        <strong>{{ __('Address') }}</strong>
                        <span>{!! nl2br(e($siteSettings->address_line ?? 'Mbezi Beach, Victoria 8007 Tanzania')) !!}</span>
                    </article>
                    <article>
                        <strong>{{ __('Email') }}</strong>
                        <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email ?? 'info@example.com' }}</a>
                    </article>
                    <article>
                        <strong>{{ __('Phone') }}</strong>
                        <a href="tel:{{ preg_replace('/\s+/', '', (string) ($siteSettings->phone ?? '')) }}">{{ $siteSettings->phone }}</a>
                    </article>
                </div>

                <a href="https://maps.google.com/?q={{ $mapQuery }}" target="_blank" class="contact-location-copy__cta">
                    {{ __('Open in Google Maps') }}
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    .site-page-top-safe {
        padding-top: 11rem !important;
    }
    .contact-location-band {
        padding: 30px 0 70px;
        background: #ffffff;
        border-top: 1px solid #eee;
    }
    .contact-location-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr);
        gap: 2rem;
        align-items: stretch;
    }
    .contact-location-map {
        min-height: 360px;
        border: 1px solid rgba(18,34,35,.12);
        overflow: hidden;
    }
    .contact-location-copy {
        border: 1px solid rgba(18,34,35,.12);
        background: #fff;
        padding: 2rem;
        display: grid;
        gap: 1rem;
        align-content: start;
    }
    .contact-location-copy__eyebrow {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #2563eb;
    }
    .contact-location-copy h2 {
        margin: 0;
        font-size: clamp(26px, 3vw, 38px);
        font-weight: 800;
        color: #051039;
        line-height: 1.1;
    }
    .contact-location-copy p {
        margin: 0;
        color: #4b5563;
        line-height: 1.75;
    }
    .contact-location-copy__stack {
        display: grid;
        gap: 1rem;
        margin-top: .5rem;
    }
    .contact-location-copy__stack article {
        display: grid;
        gap: .35rem;
        padding: 1rem 1rem .9rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }
    .contact-location-copy__stack strong {
        color: #051039;
    }
    .contact-location-copy__stack span,
    .contact-location-copy__stack a {
        color: #4b5563;
        text-decoration: none;
    }
    .contact-location-copy__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .9rem 1.2rem;
        background: #051039;
        color: #fff !important;
        text-decoration: none;
        font-weight: 700;
        width: max-content;
    }
    @media (max-width: 991px) {
        .site-page-top-safe {
            padding-top: 8.6rem !important;
        }
        .contact-location-grid {
            grid-template-columns: 1fr;
        }
        .contact-location-map {
            min-height: 300px;
        }
    }
</style>
@endsection
