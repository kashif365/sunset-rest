@extends('layouts.public')

@section('seo_title'){{ $page?->seo_title ?: 'About Us — Sunset Bagel Exchange | Ocean, NJ' }}@endsection
@section('meta_description'){{ $page?->meta_description ?: 'The story behind Sunset Bagel Exchange: hand rolled, kettle boiled, old fashioned bagels made fresh every morning in Ocean Township, NJ.' }}@endsection

@section('content')
    <section class="section-burgundy py-5">
        <div class="container text-center">
            <p class="eyebrow text-yellow mb-1">Our story</p>
            <h1 class="display-heading display-5 text-white">{{ $page?->title ?: 'About Sunset Bagel Exchange' }}</h1>
        </div>
    </section>
    @include('partials.curve', ['fill' => '#69001F', 'flip' => true])

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-12 col-lg-7">
                @if($page?->content)
                    <div class="fs-5">{!! $page->content !!}</div>
                @else
                    <p class="fs-5">
                        Sunset Bagel Exchange is Ocean Township's neighborhood bagel shop. Every morning our bakers
                        roll the dough by hand and boil each bagel in the kettle before baking — the old-fashioned
                        way that gives a real bagel its shiny crust and chewy bite.
                    </p>
                    <p class="fs-5">
                        From two-egg breakfast sandwiches to stacked Boar's Head lunch classics, homemade salads and
                        a little taste of Latin culture, everything is made fresh to order.
                    </p>
                @endif
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('menu.index') }}" class="btn btn-brand">See the Menu</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary">Visit Us</a>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="row g-2">
                    @foreach($gallery as $image)
                        <div class="col-6 gallery-grid">
                            <img src="{{ \App\Services\ImageService::thumbUrl($image->image) }}" alt="{{ $image->image_alt ?: 'Sunset Bagel Exchange' }}" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
