@extends('layouts.public')

@section('seo_title')Catering — Sunset Bagel Exchange | Ocean, NJ@endsection
@section('meta_description')Bagel platters, breakfast spreads and lunch catering for family gatherings and business meetings in Ocean Township, NJ. Let us make your meeting a success.@endsection

@section('content')
    <section class="section-red py-5">
        <div class="container text-center">
            <p class="eyebrow text-yellow mb-1">Breakfast &amp; lunch options</p>
            <h1 class="display-heading display-5 text-white">Catering</h1>
            <p class="lead mx-auto text-white" style="max-width: 38rem;">
                Delicious spreads for your family gathering or business meeting — let us help make it a success!
            </p>
            <a class="btn btn-gold btn-lg mt-2" href="{{ route('contact') }}">Request a Quote</a>
        </div>
    </section>
    @include('partials.curve', ['fill' => '#B51F2A', 'flip' => true])

    <div class="container py-5">
        @if($page?->content)
            <div class="mb-5">{!! $page->content !!}</div>
        @endif

        @if($packages->isEmpty())
            <p class="text-center text-body-secondary py-4">Our catering menu is being finalized — call us at (732) 361-8119 and we'll put something together for you.</p>
        @else
            <div class="row g-4">
                @foreach($packages as $package)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <img src="{{ \App\Services\ImageService::thumbUrl($package->image) }}" class="card-img-top object-cover" style="aspect-ratio: 16/10;"
                                 alt="{{ $package->image_alt ?: $package->name }}" loading="lazy">
                            <div class="card-body d-flex flex-column">
                                <h2 class="h5 fw-bold text-uppercase">{{ $package->name }}
                                    @if($package->needs_verification)
                                        <span class="badge badge-verify rounded-pill ms-1">Ask for pricing</span>
                                    @endif
                                </h2>
                                @if($package->serves)
                                    <p class="small text-body-secondary mb-1"><i class="bi bi-people-fill me-1" aria-hidden="true"></i>Serves {{ $package->serves }}</p>
                                @endif
                                @if($package->description)
                                    <p class="text-body-secondary">{{ $package->description }}</p>
                                @endif
                                <div class="mt-auto d-flex align-items-center justify-content-between">
                                    <span class="price">
                                        @if($package->price !== null && ! $package->needs_verification)
                                            ${{ number_format((float) $package->price, 2) }}
                                            @if($package->price_label)<span class="small text-muted fw-normal">{{ $package->price_label }}</span>@endif
                                        @else
                                            <span class="fs-6">Call for pricing</span>
                                        @endif
                                    </span>
                                    <a href="{{ route('contact') }}" class="btn btn-brand btn-sm">Inquire</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="p-4 p-lg-5 rounded-4 mt-5 text-center text-white" style="background: linear-gradient(105deg, #69001F, #B51F2A);">
            <h2 class="display-heading h2 text-white mb-2">Planning something big?</h2>
            <p class="mb-3">Call us and our team will build a custom package for your headcount and budget.</p>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}" class="btn btn-gold btn-lg">
                <i class="bi bi-telephone-fill me-2" aria-hidden="true"></i>{{ $siteSettings->get('business_phone', '(732) 361-8119') }}
            </a>
        </div>
    </div>
@endsection
