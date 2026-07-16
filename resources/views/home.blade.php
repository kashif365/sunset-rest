@extends('layouts.public')

@section('seo_title'){{ $siteSettings->get('seo_title', 'Sunset Bagel Exchange — Hand Rolled Bagels, Breakfast & Lunch in Ocean, NJ') }}@endsection


@push('structured-data')
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->restaurant(), JSON_UNESCAPED_SLASHES)</script>
@endpush

@section('content')

    {{-- ============ HERO SLIDER (full width, admin managed) ============ --}}
    @if($slides->isNotEmpty())
        <section class="hero-slider" aria-label="Highlights">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
                @if($slides->count() > 1)
                    <div class="carousel-indicators">
                        @foreach($slides as $slide)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}"
                                    class="{{ $loop->first ? 'active' : '' }}"
                                    @if($loop->first) aria-current="true" @endif
                                    aria-label="Slide {{ $loop->iteration }}"></button>
                        @endforeach
                    </div>
                @endif

                <div class="carousel-inner">
                    @foreach($slides as $slide)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}"
                             style="background-image: url('{{ \App\Services\ImageService::url($slide->image, '/images/hero-default.svg') }}')"
                             role="group" aria-roledescription="slide" aria-label="{{ $slide->image_alt ?: $slide->title }}">
                            <div class="hero-overlay" aria-hidden="true"></div>
                            <div class="container hero-content">
                                @if($loop->first)
                                    <h1>{{ $slide->title }}</h1>
                                @else
                                    <h2>{{ $slide->title }}</h2>
                                @endif
                                @if($slide->subtitle)
                                    <p class="lead">{{ $slide->subtitle }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @if($slide->button_text && $slide->button_url)
                                        <a href="{{ url($slide->button_url) }}" class="btn btn-gold btn-lg">{{ $slide->button_text }}</a>
                                    @endif
                                    @if($slide->button2_text && $slide->button2_url)
                                        <a href="{{ url($slide->button2_url) }}" class="btn btn-outline-cream btn-lg">{{ $slide->button2_text }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($slides->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-icon-circle"><i class="bi bi-chevron-left text-white fs-4" aria-hidden="true"></i></span>
                        <span class="visually-hidden">Previous slide</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-icon-circle"><i class="bi bi-chevron-right text-white fs-4" aria-hidden="true"></i></span>
                        <span class="visually-hidden">Next slide</span>
                    </button>
                @endif
            </div>
        </section>
    @endif

    {{-- ============ INTRO / CTA STRIP ============ --}}
    <section class="section-cream py-5 text-center">
        <div class="container">
            <p class="eyebrow mb-2">Ocean Township, New Jersey</p>
            <h2 class="display-heading display-5 text-burgundy mb-3">
                Hand Rolled... Kettle Boiled<br>
                <span class="swoosh-underline">Old Fashioned Bagels</span>
            </h2>
            <p class="lead mx-auto" style="max-width: 40rem;">
                Breakfast, lunch and coffee done the old-school way — dough rolled by hand every morning
                and boiled in the kettle before it ever sees the oven.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}" class="btn btn-brand btn-lg">
                    <i class="bi bi-telephone-fill me-2" aria-hidden="true"></i>Call Ahead — Quick Pickup
                </a>
                <a href="{{ route('menu.index') }}" class="btn btn-gold btn-lg">
                    <i class="bi bi-bag-check-fill me-2" aria-hidden="true"></i>Order Online
                </a>
                <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary btn-lg">View Menu</a>
            </div>
        </div>
    </section>

    {{-- ============ FEATURED CATEGORIES ============ --}}
    @if($featuredCategories->isNotEmpty())
        @include('partials.curve', ['fill' => '#F8A51B'])
        <section class="section-white py-5" aria-labelledby="categories-heading">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
                    <div>
                        <p class="eyebrow mb-1">What are you craving?</p>
                        <h2 id="categories-heading" class="section-heading h1 mb-0">Explore the Menu</h2>
                    </div>
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Full Menu <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
                </div>
                <div class="row g-3 g-lg-4">
                    @foreach($featuredCategories as $category)
                        <div class="col-6 col-md-4 col-lg-2">
                            <a class="category-card" href="{{ route('menu.category', $category) }}">
                                <img class="category-card-img" src="{{ \App\Services\ImageService::thumbUrl($category->image) }}"
                                     alt="{{ $category->image_alt ?: $category->name }}" loading="lazy" width="480" height="360">
                                <span class="category-card-body">
                                    <span>{{ $category->name }}</span>
                                    <i class="bi bi-arrow-right-circle-fill text-gold" aria-hidden="true"></i>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FEATURED / BESTSELLERS ============ --}}
    @if($featuredItems->isNotEmpty())
        <section class="section-cream py-5" aria-labelledby="featured-heading">
            <div class="container">
                <div class="text-center mb-4">
                    <p class="eyebrow mb-1">Local favorites</p>
                    <h2 id="featured-heading" class="section-heading h1">Featured &amp; Bestsellers</h2>
                </div>
                <div class="row g-3 g-lg-4">
                    @foreach($featuredItems as $item)
                        <div class="col-12 col-sm-6 col-lg-3">
                            @include('partials.product-card', ['item' => $item])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ BREAKFAST / LUNCH / COFFEE PROMO ============ --}}
    @include('partials.curve', ['fill' => '#B51F2A'])
    <section class="section-red py-5" aria-labelledby="daypart-heading">
        <div class="container text-center">
            <h2 id="daypart-heading" class="display-heading display-6 mb-4 text-white">Breakfast &bull; Lunch &bull; Coffee</h2>
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="why-tile">
                        <span class="why-icon"><i class="bi bi-egg-fried" aria-hidden="true"></i></span>
                        <h3 class="h5 text-uppercase fw-bold">Breakfast All Morning</h3>
                        <p class="mb-0">Two-egg sandwiches, omelettes, French toast and hash browns — hot off the griddle.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="why-tile">
                        <span class="why-icon"><i class="bi bi-basket2-fill" aria-hidden="true"></i></span>
                        <h3 class="h5 text-uppercase fw-bold">Stacked Lunch Classics</h3>
                        <p class="mb-0">Boar's Head cold cuts, foot-long chicken sandwiches, burgers and homemade salads.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="why-tile">
                        <span class="why-icon"><i class="bi bi-cup-hot-fill" aria-hidden="true"></i></span>
                        <h3 class="h5 text-uppercase fw-bold">Fresh Coffee</h3>
                        <p class="mb-0">Your morning cup, brewed all day — the perfect match for a warm bagel.</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('menu.index') }}" class="btn btn-gold btn-lg mt-4">Browse the Full Menu</a>
        </div>
    </section>

    {{-- ============ SPECIAL OFFERS ============ --}}
    @if($offers->isNotEmpty() || $banner)
        <section class="section-cream py-5" aria-labelledby="offers-heading">
            <div class="container">
                <h2 id="offers-heading" class="section-heading h1 text-center mb-4">Specials &amp; Deals</h2>

                @if($banner)
                    <div class="p-4 p-lg-5 rounded-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(105deg, #69001F, #B51F2A);">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-lg-8">
                                @if($banner->badge_text)
                                    <span class="badge badge-featured rounded-pill mb-2 fs-6">{{ $banner->badge_text }}</span>
                                @endif
                                <h3 class="display-heading h2 text-white">{{ $banner->title }}</h3>
                                @if($banner->description)
                                    <p class="mb-0">{{ $banner->description }}</p>
                                @endif
                            </div>
                            <div class="col-12 col-lg-4 text-lg-end">
                                @if($banner->button_text && $banner->button_url)
                                    <a href="{{ url($banner->button_url) }}" class="btn btn-gold btn-lg">{{ $banner->button_text }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    @foreach($offers as $offer)
                        <div class="col-12 col-md-4">
                            <div class="card h-100">
                                @if($offer->image)
                                    <img src="{{ \App\Services\ImageService::thumbUrl($offer->image) }}" class="card-img-top object-cover" style="aspect-ratio: 16/9;" alt="{{ $offer->image_alt ?: $offer->title }}" loading="lazy">
                                @endif
                                <div class="card-body">
                                    @if($offer->badge_text)
                                        <span class="badge badge-bestseller rounded-pill mb-2">{{ $offer->badge_text }}</span>
                                    @endif
                                    <h3 class="h5 fw-bold text-uppercase">{{ $offer->title }}</h3>
                                    @if($offer->description)
                                        <p class="text-body-secondary mb-2">{{ $offer->description }}</p>
                                    @endif
                                    @if($offer->button_text && $offer->button_url)
                                        <a href="{{ url($offer->button_url) }}" class="btn btn-brand btn-sm">{{ $offer->button_text }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CATERING CALLOUT ============ --}}
    @include('partials.curve', ['fill' => '#69001F'])
    <section class="section-burgundy py-5" aria-labelledby="catering-heading">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-7">
                    <p class="eyebrow text-yellow mb-1">Feeding a crowd?</p>
                    <h2 id="catering-heading" class="display-heading h1 text-white">Catering for Family Gatherings &amp; Business Meetings</h2>
                    <p class="lead mb-0">Bagel spreads, breakfast platters and lunch boxes delivered fresh. Let us help make your meeting a success.</p>
                </div>
                <div class="col-12 col-lg-5 text-lg-end">
                    <a href="{{ route('catering') }}" class="btn btn-gold btn-lg me-2">See Catering Menu</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-cream btn-lg mt-2 mt-sm-0">Talk to Us</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ WHY CHOOSE US ============ --}}
    <section class="section-white py-5" aria-labelledby="why-heading">
        <div class="container text-center">
            <h2 id="why-heading" class="section-heading h1 mb-4">Why Sunset Bagel Exchange?</h2>
            <div class="row g-4">
                @foreach([
                    ['icon' => 'bi-hand-index-thumb', 'title' => 'Hand-Rolled Bagels', 'text' => 'Every bagel is rolled by hand each morning — never frozen, never factory-made.'],
                    ['icon' => 'bi-droplet-half', 'title' => 'Kettle Boiled', 'text' => 'Boiled the old-fashioned way for that shiny crust and chewy center.'],
                    ['icon' => 'bi-basket3-fill', 'title' => 'Fresh Ingredients', 'text' => 'Boar\'s Head premium cold cuts, fresh produce and homemade salads.'],
                    ['icon' => 'bi-emoji-smile-fill', 'title' => 'Friendly Local Service', 'text' => 'A neighborhood shop that knows your order — keeping Ocean Township fed.'],
                ] as $why)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="why-tile border" style="background: #FFF5D8;">
                            <span class="why-icon"><i class="bi {{ $why['icon'] }}" aria-hidden="true"></i></span>
                            <h3 class="h6 text-uppercase fw-bold">{{ $why['title'] }}</h3>
                            <p class="mb-0 text-body-secondary">{{ $why['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HOURS + MAP ============ --}}
    <section class="section-cream py-5" aria-labelledby="visit-heading">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-5">
                    <h2 id="visit-heading" class="section-heading h1 mb-3">Visit Us</h2>
                    <p class="mb-1"><i class="bi bi-geo-alt-fill text-gold me-2" aria-hidden="true"></i>{{ $siteSettings->get('business_address', '3316 Sunset Ave., Ocean, NJ 07712') }}</p>
                    <p class="mb-3">
                        <i class="bi bi-telephone-fill text-gold me-2" aria-hidden="true"></i>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}" class="fw-bold">{{ $siteSettings->get('business_phone', '(732) 361-8119') }}</a>
                    </p>
                    <p class="mb-3">
                        @if($isOpenNow)
                            <span class="badge rounded-pill text-bg-success fs-6"><i class="bi bi-door-open-fill me-1" aria-hidden="true"></i>Open now</span>
                        @else
                            <span class="badge rounded-pill text-bg-secondary fs-6"><i class="bi bi-door-closed-fill me-1" aria-hidden="true"></i>Currently closed</span>
                        @endif
                    </p>
                    <table class="hours-table w-100 mb-0">
                        <caption class="visually-hidden">Weekly store hours</caption>
                        <tbody>
                        @foreach($hours as $hour)
                            <tr class="{{ (int) now()->dayOfWeek === (int) $hour->day_of_week ? 'today' : '' }}">
                                <th scope="row" class="text-start">{{ $hour->dayName() }}</th>
                                <td class="text-end">
                                    @if($hour->is_closed)
                                        Closed
                                    @else
                                        {{ \Carbon\Carbon::parse($hour->open_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($hour->close_time)->format('g:i A') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-lg-7">
                    @if($siteSettings->get('map_embed_url'))
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden border h-100">
                            <iframe src="{{ $siteSettings->get('map_embed_url') }}" title="Map to Sunset Bagel Exchange"
                                    loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    @else
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden border bg-white d-flex">
                            <div class="d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <i class="bi bi-map fs-1 text-gold mb-2" aria-hidden="true"></i>
                                <p class="mb-0">3316 Sunset Ave., Ocean, NJ 07712</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ============ GALLERY ============ --}}
    @if($gallery->isNotEmpty())
        <section class="section-white py-5" aria-labelledby="gallery-heading">
            <div class="container">
                <div class="text-center mb-4">
                    <p class="eyebrow mb-1">Fresh from the kitchen</p>
                    <h2 id="gallery-heading" class="section-heading h1">Follow Us <span class="text-gold">@sunsetbagelexchange</span></h2>
                </div>
                <div class="row g-2 g-md-3 gallery-grid">
                    @foreach($gallery as $image)
                        <div class="col-6 col-md-3">
                            <img src="{{ \App\Services\ImageService::thumbUrl($image->image) }}" alt="{{ $image->image_alt ?: $image->title ?: 'Sunset Bagel Exchange photo' }}" loading="lazy" width="480" height="480">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
