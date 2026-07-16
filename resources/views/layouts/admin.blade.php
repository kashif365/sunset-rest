<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — SBE Admin</title>
    <link rel="icon" href="/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Roboto+Condensed:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Admin navigation">
        <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">🥯 Sunset Bagel<br>Exchange Admin</a>

        <nav>
            <div class="nav-section">Overview</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt"></i>Orders</a>
            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Customers</a>
            <a class="nav-link {{ request()->routeIs('admin.contact-submissions.*') ? 'active' : '' }}" href="{{ route('admin.contact-submissions.index') }}"><i class="bi bi-chat-left-dots"></i>Messages</a>

            @can('manage-content')
                <div class="nav-section">Menu</div>
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-grid"></i>Categories</a>
                <a class="nav-link {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}" href="{{ route('admin.menu-items.index') }}"><i class="bi bi-egg-fried"></i>Menu Items</a>
                <a class="nav-link {{ request()->routeIs('admin.modifier-groups.*') ? 'active' : '' }}" href="{{ route('admin.modifier-groups.index') }}"><i class="bi bi-ui-checks"></i>Modifiers</a>
                <a class="nav-link {{ request()->routeIs('admin.dietary-labels.*') ? 'active' : '' }}" href="{{ route('admin.dietary-labels.index') }}"><i class="bi bi-heart-pulse"></i>Dietary Labels</a>
                <a class="nav-link {{ request()->routeIs('admin.allergens.*') ? 'active' : '' }}" href="{{ route('admin.allergens.index') }}"><i class="bi bi-exclamation-diamond"></i>Allergens</a>

                <div class="nav-section">Marketing</div>
                <a class="nav-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}" href="{{ route('admin.hero-slides.index') }}"><i class="bi bi-images"></i>Hero Slides</a>
                <a class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}"><i class="bi bi-megaphone"></i>Promotions</a>
                <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated"></i>Coupons</a>
                <a class="nav-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}" href="{{ route('admin.subscribers.index') }}"><i class="bi bi-envelope-heart"></i>Subscribers</a>

                <div class="nav-section">Content</div>
                <a class="nav-link {{ request()->routeIs('admin.catering-packages.*') ? 'active' : '' }}" href="{{ route('admin.catering-packages.index') }}"><i class="bi bi-box-seam"></i>Catering</a>
                <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}"><i class="bi bi-question-circle"></i>FAQs</a>
                <a class="nav-link {{ request()->routeIs('admin.gallery-images.*') ? 'active' : '' }}" href="{{ route('admin.gallery-images.index') }}"><i class="bi bi-camera"></i>Gallery</a>
                <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}"><i class="bi bi-file-earmark-text"></i>Pages</a>
                <a class="nav-link {{ request()->routeIs('admin.navigation-links.*') ? 'active' : '' }}" href="{{ route('admin.navigation-links.index') }}"><i class="bi bi-list-nested"></i>Navigation</a>
            @endcan

            @can('manage-settings')
                <div class="nav-section">Configuration</div>
                <a class="nav-link {{ request()->routeIs('admin.hours.*') ? 'active' : '' }}" href="{{ route('admin.hours.edit') }}"><i class="bi bi-clock-history"></i>Business Hours</a>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit', 'business') }}"><i class="bi bi-gear"></i>Settings</a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-person-badge"></i>Users &amp; Roles</a>
            @endcan
        </nav>
        <div class="p-3"></div>
    </aside>

    <div class="admin-main d-flex flex-column">
        <header class="admin-topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>View site
                </a>
                <span class="d-none d-md-inline text-body-secondary small">
                    {{ auth()->user()->name }} <span class="badge text-bg-secondary">{{ auth()->user()->roleLabel() }}</span>
                </span>
                <form action="{{ route('admin.logout') }}" method="post">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </header>

        <main class="p-3 p-lg-4 flex-grow-1">
            @include('partials.flash')

            @if($errors->any() && ! request()->routeIs('admin.login'))
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
