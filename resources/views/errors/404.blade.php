@extends('layouts.plain')

@section('title', __('Page not found'))

@push('head')
<style>
  .err-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.25rem;
    background: linear-gradient(165deg, #0f172a 0%, #1e293b 42%, #334155 100%);
    box-sizing: border-box;
  }
  .err-card {
    max-width: 28rem;
    width: 100%;
    text-align: center;
    padding: 2.5rem 1.75rem;
    border-radius: 20px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    backdrop-filter: blur(12px);
    box-shadow: 0 24px 64px rgba(0,0,0,.25);
  }
  .err-code {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(4rem, 14vw, 6rem);
    font-weight: 600;
    line-height: 1;
    margin: 0;
    color: rgba(255,255,255,.95);
    letter-spacing: -0.02em;
  }
  .err-title {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 600;
    margin: 1rem 0 0;
    color: #fff;
  }
  .err-text {
    font-size: 1rem;
    line-height: 1.55;
    margin: 1rem 0 0;
    color: rgba(255,255,255,.82);
  }
  .err-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 1.75rem;
    padding: 0.65rem 1.35rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    color: #0f172a;
    background: #fff;
    border: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .err-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.2); }
</style>
@endpush

@section('content')
<div class="err-page">
  <div class="err-card">
    <p class="err-code">404</p>
    <h1 class="err-title">{{ __('Page not found') }}</h1>
    <p class="err-text">{{ __('Sorry — we could not find that page. It may have been moved or the link is outdated.') }}</p>
    <a href="{{ url('/') }}" class="err-btn">{{ __('Back to home') }}</a>
  </div>
</div>
@endsection
