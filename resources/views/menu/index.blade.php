@extends('layouts.public')

@section('seo_title')Full Menu — Sunset Bagel Exchange | Ocean, NJ@endsection
@section('meta_description')Browse our full menu: hand rolled bagels, breakfast sandwiches, omelettes, lunch classics, salads, burritos and fresh bakery. Order online for pickup.@endsection

@php
    $breadcrumbTrail = ['Home' => route('home'), 'Menu' => route('menu.index')];
@endphp
@push('structured-data')
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->breadcrumbs($breadcrumbTrail), JSON_UNESCAPED_SLASHES)</script>
@endpush

@section('content')
    <div x-data="menuFilter()" x-init="search = @js($search); apply()">

        <section class="section-burgundy py-4 py-lg-5">
            <div class="container">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h1 class="display-heading display-5 text-white mb-1">Our Menu</h1>
                        <p class="mb-0 text-white-50">Hand rolled bagels, hot griddle breakfast and stacked lunch classics.</p>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <label class="visually-hidden" for="menu-search">Search the menu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                                    <input id="menu-search" type="search" class="form-control" placeholder="Search the menu…"
                                           x-model.debounce.200ms="search" @input="apply()" inputmode="search">
                                </div>
                            </div>
                            <div class="col-7 col-sm-4">
                                <label class="visually-hidden" for="menu-diet">Dietary filter</label>
                                <select id="menu-diet" class="form-select" x-model="diet" @change="apply()">
                                    <option value="">All diets</option>
                                    @foreach($dietaryLabels as $label)
                                        <option value="{{ $label->slug }}">{{ $label->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5 col-sm-2 d-flex align-items-center">
                                <div class="form-check text-white mb-0">
                                    <input class="form-check-input" type="checkbox" id="menu-available" x-model="availableOnly" @change="apply()">
                                    <label class="form-check-label" for="menu-available">In&nbsp;stock</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sticky category pills --}}
        <nav class="menu-category-nav" aria-label="Menu categories">
            <div class="container">
                <div class="nav-scroller" role="list">
                    @foreach($categories as $category)
                        <a class="cat-pill" role="listitem" href="#cat-{{ $category->slug }}">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="container py-4 py-lg-5">
            @foreach($categories as $category)
                <section id="cat-{{ $category->slug }}" class="menu-anchor mb-5" data-menu-section aria-labelledby="cat-heading-{{ $category->slug }}">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 id="cat-heading-{{ $category->slug }}" class="ribbon-heading h4 mb-0">{{ $category->name }}</h2>
                        <a class="small fw-bold" href="{{ route('menu.category', $category) }}">View category <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                    @if($category->description)
                        <p class="text-body-secondary mb-3">{{ $category->description }}</p>
                    @endif
                    <div class="row g-3 g-lg-4">
                        @foreach($category->menuItems as $item)
                            <div class="col-12 col-sm-6 col-lg-3 d-flex">
                                @include('partials.product-card', ['item' => $item])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <p class="text-center text-body-secondary" x-show="search || diet || availableOnly" x-cloak>
                Can't find it? <button type="button" class="btn btn-link p-0 align-baseline" @click="search=''; diet=''; availableOnly=false; apply()">Clear all filters</button>
            </p>
        </div>
    </div>
@endsection
