@extends('layouts.public')

@section('seo_title'){{ $category->seo_title ?: $category->name.' — Sunset Bagel Exchange Menu' }}@endsection
@section('meta_description'){{ $category->meta_description ?: ($category->description ?: 'Explore '.$category->name.' at Sunset Bagel Exchange in Ocean, NJ. Order online for pickup.') }}@endsection

@php
    $breadcrumbTrail = ['Home' => route('home'), 'Menu' => route('menu.index'), $category->name => route('menu.category', $category)];
@endphp
@push('structured-data')
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->breadcrumbs($breadcrumbTrail), JSON_UNESCAPED_SLASHES)</script>
@endpush

@section('content')
    <section class="section-burgundy py-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a class="link-light" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="link-light" href="{{ route('menu.index') }}">Menu</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
            <h1 class="display-heading display-6 text-white mb-1">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-white-50 mb-0">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <nav class="menu-category-nav" aria-label="Menu categories">
        <div class="container">
            <div class="nav-scroller" role="list">
                @foreach($categories as $cat)
                    <a class="cat-pill {{ $cat->is($category) ? 'active' : '' }}" role="listitem"
                       href="{{ route('menu.category', $cat) }}" @if($cat->is($category)) aria-current="page" @endif>{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>

    <div class="container py-4 py-lg-5">
        @if($category->menuItems->isEmpty())
            <p class="text-center text-body-secondary py-5">Nothing in this category just yet — check back soon!</p>
        @else
            <div class="row g-3 g-lg-4">
                @foreach($category->menuItems as $item)
                    <div class="col-12 col-sm-6 col-lg-3 d-flex">
                        @include('partials.product-card', ['item' => $item])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
