<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $businessName = $siteSettings->get('business_name', 'Sunset Bagel Exchange');
        $pageTitle = trim($__env->yieldContent('seo_title')) ?: $siteSettings->get('seo_title', $businessName.' — Hand Rolled Bagels in Ocean, NJ');
        $pageDescription = trim($__env->yieldContent('meta_description')) ?: $siteSettings->get('seo_description', 'Hand rolled, kettle boiled, old fashioned bagels. Breakfast, lunch and coffee in Ocean Township, NJ.');
        $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
        $ogImage = trim($__env->yieldContent('og_image')) ?: \App\Services\ImageService::url($siteSettings->get('og_image_path'), '/images/og-default.svg');
    @endphp

    {{-- $pageTitle should exist from the @php block above; provide fallback to avoid 500s in edge-cases. --}}
    <title>{{ $pageTitle ?? $siteSettings->get('seo_title', 'Sunset Bagel Exchange — Hand Rolled Bagels') }}</title>
<meta name="description" content="{{ $pageDescription ?? $siteSettings->get('seo_description', 'Hand rolled, kettle boiled, old fashioned bagels. Breakfast, lunch and coffee in Ocean Township, NJ.') }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:site_name" content="{{ $businessName }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="icon" href="{{ \App\Services\ImageService::url($siteSettings->get('favicon_path'), '/images/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto+Condensed:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @stack('structured-data')
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

@if($siteSettings->bool('announcement_enabled') && $siteSettings->get('announcement_text'))
    <div class="announcement-bar" role="region" aria-label="Announcement">
        {{ $siteSettings->get('announcement_text') }}
    </div>
@endif

<nav class="navbar navbar-expand-lg navbar-dark navbar-sbe sticky-top" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ \App\Services\ImageService::url($siteSettings->get('logo_path'), '/images/logo.svg') }}" alt="{{ $businessName }} logo">
            <span class="d-none d-sm-inline">Sunset Bagel<br class="d-lg-none"> Exchange</span>
        </a>

        <div class="d-flex align-items-center order-lg-3 gap-1">
            <a class="nav-icon-btn d-none d-md-inline-flex" href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}"
               aria-label="Call us at {{ $siteSettings->get('business_phone', '(732) 361-8119') }}">
                <i class="bi bi-telephone-fill" aria-hidden="true"></i>
            </a>
            <a class="nav-icon-btn" href="{{ route('cart.index') }}" aria-label="View cart, {{ $cartCount }} items">
                <i class="bi bi-bag-fill" aria-hidden="true"></i>
                @if($cartCount > 0)
                    <span class="cart-count" aria-hidden="true">{{ $cartCount }}</span>
                @endif
            </a>
            <button class="navbar-toggler ms-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="offcanvas offcanvas-end navbar-sbe-offcanvas order-lg-2" tabindex="-1" id="mainNav" aria-labelledby="mainNavLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title" id="mainNavLabel">Menu</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#mainNav" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    @forelse($headerLinks as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ url()->current() === url($link->url) ? 'active' : '' }}"
                               href="{{ url($link->url) }}" @if($link->new_tab) target="_blank" rel="noopener" @endif>
                                {{ $link->label }}
                            </a>
                        </li>
                    @empty
                        <li class="nav-item"><a class="nav-link" href="{{ route('menu.index') }}">Menu</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('menu.index') }}">Order Online</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('catering') }}">Catering</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                    @endforelse
                </ul>
                <a href="{{ route('menu.index') }}" class="btn btn-gold d-flex d-lg-inline-flex align-items-center justify-content-center">
                    Order Online
                </a>
            </div>
        </div>
    </div>
</nav>

@include('partials.flash')

<main id="main-content">
    @yield('content')
</main>

<footer class="footer-sbe pt-5 mt-auto">
    <div class="container">
        <div class="row gy-4 pb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <p class="footer-heading">Sunset Bagel Exchange</p>
                <p class="mb-2">Hand rolled... kettle boiled, old fashioned bagels. Breakfast, lunch &amp; coffee in Ocean Township.</p>
                <p class="mb-1"><i class="bi bi-geo-alt-fill me-2 text-gold" aria-hidden="true"></i>{{ $siteSettings->get('business_address', '3316 Sunset Ave., Ocean, NJ 07712') }}</p>
                <p class="mb-1">
                    <i class="bi bi-telephone-fill me-2 text-gold" aria-hidden="true"></i>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}">{{ $siteSettings->get('business_phone', '(732) 361-8119') }}</a>
                </p>
                <p class="mb-3">
                    <i class="bi bi-envelope-fill me-2 text-gold" aria-hidden="true"></i>
                    <a href="mailto:{{ $siteSettings->get('business_email', 'sunsetbagelexchange@gmail.com') }}">{{ $siteSettings->get('business_email', 'sunsetbagelexchange@gmail.com') }}</a>
                </p>
                <div class="d-flex gap-2">
                    @if($siteSettings->get('facebook_url'))
                        <a class="social-btn" href="{{ $siteSettings->get('facebook_url') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    @endif
                    @if($siteSettings->get('instagram_url'))
                        <a class="social-btn" href="{{ $siteSettings->get('instagram_url') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    @endif
                    @if($siteSettings->get('tiktok_url'))
                        <a class="social-btn" href="{{ $siteSettings->get('tiktok_url') }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>

            <div class="col-6 col-md-6 col-lg-2">
                <p class="footer-heading">Quick Links</p>
                <ul class="list-unstyled">
                    @forelse($footerLinks as $link)
                        <li class="mb-2"><a href="{{ url($link->url) }}" @if($link->new_tab) target="_blank" rel="noopener" @endif>{{ $link->label }}</a></li>
                    @empty
                        <li class="mb-2"><a href="{{ route('menu.index') }}">Full Menu</a></li>
                        <li class="mb-2"><a href="{{ route('catering') }}">Catering</a></li>
                        <li class="mb-2"><a href="{{ route('faq') }}">FAQ</a></li>
                    @endforelse
                </ul>
            </div>

            <div class="col-6 col-md-6 col-lg-3">
                <p class="footer-heading">Hours</p>
                <ul class="list-unstyled small">
                    @foreach($footerHours as $hour)
                        <li class="d-flex justify-content-between gap-2 mb-1 {{ (int) now()->dayOfWeek === (int) $hour->day_of_week ? 'fw-bold text-yellow' : '' }}">
                            <span>{{ $hour->dayName() }}</span>
                            <span>
                                @if($hour->is_closed)
                                    Closed
                                @else
                                    {{ \Carbon\Carbon::parse($hour->open_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($hour->close_time)->format('g:i A') }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <p class="footer-heading">Fresh Bagel News</p>
                <p class="small">Sign up for specials, seasonal cream cheeses and catering deals.</p>
                <form action="{{ route('subscribe') }}" method="post" class="d-flex flex-column gap-2">
                    @csrf
                    <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <label class="visually-hidden" for="footer-subscribe-email">Email address</label>
                    <input id="footer-subscribe-email" type="email" name="email" class="form-control" placeholder="you@email.com" inputmode="email" autocomplete="email" required>
                    <button class="btn btn-gold" type="submit">Sign Me Up</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom py-3 d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</span>
            <span>
                <a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a>
                <span class="mx-2" aria-hidden="true">•</span>
                <a href="{{ route('page.show', 'terms-and-conditions') }}">Terms &amp; Conditions</a>
            </span>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
