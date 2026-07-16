@extends('layouts.public')

@section('seo_title')Ordering Currently Closed — Sunset Bagel Exchange@endsection

@section('content')
    <div class="container py-5 text-center" style="max-width: 40rem;">
        <i class="bi bi-moon-stars-fill display-1 text-gold" aria-hidden="true"></i>
        <h1 class="display-heading h1 text-burgundy mt-3">Online Ordering Is Closed Right Now</h1>
        <p class="lead">
            We're not accepting online orders at the moment. Give us a call at
            <a class="fw-bold" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}">{{ $siteSettings->get('business_phone', '(732) 361-8119') }}</a>
            or stop by during business hours.
        </p>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="{{ route('menu.index') }}" class="btn btn-brand">Browse the Menu</a>
            <a href="{{ route('contact') }}" class="btn btn-outline-secondary">Hours &amp; Location</a>
        </div>
    </div>
@endsection
