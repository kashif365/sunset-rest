@extends('layouts.public')

@section('seo_title'){{ $menuItem->seo_title ?: $menuItem->name.' — Sunset Bagel Exchange' }}@endsection
@section('meta_description'){{ $menuItem->meta_description ?: ($menuItem->short_description ?: 'Order '.$menuItem->name.' for pickup from Sunset Bagel Exchange in Ocean, NJ.') }}@endsection
@section('og_type', 'product')
@section('og_image'){{ \App\Services\ImageService::url($menuItem->image, '/images/og-default.svg') }}@endsection

@php
    $breadcrumbTrail = [
        'Home' => route('home'),
        'Menu' => route('menu.index'),
        $menuItem->category->name => route('menu.category', $menuItem->category),
        $menuItem->name => route('menu.item', $menuItem),
    ];
@endphp
@push('structured-data')
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->menuItem($menuItem), JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->breadcrumbs($breadcrumbTrail), JSON_UNESCAPED_SLASHES)</script>
@endpush

@section('content')
    <div class="container py-4 py-lg-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
                <li class="breadcrumb-item"><a href="{{ route('menu.category', $menuItem->category) }}">{{ $menuItem->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $menuItem->name }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5"
             x-data="itemConfigurator({{ $menuItem->effectivePrice() }}, {{ $menuItem->variations->map(fn ($v) => ['id' => $v->id, 'price' => (float) $v->price])->toJson() }})">
            <div class="col-12 col-lg-6">
                <div class="rounded-4 overflow-hidden border position-relative">
                    <img src="{{ \App\Services\ImageService::url($menuItem->image) }}"
                         alt="{{ $menuItem->image_alt ?: $menuItem->name }}"
                         class="w-100 object-cover" style="aspect-ratio: 4/3;">
                    <div class="badge-stack position-absolute top-0 start-0 m-3 d-flex flex-column gap-2 align-items-start">
                        @if($menuItem->is_featured)<span class="badge badge-featured rounded-pill">Featured</span>@endif
                        @if($menuItem->is_bestseller)<span class="badge badge-bestseller rounded-pill">Bestseller</span>@endif
                        @if($menuItem->needs_verification)<span class="badge badge-verify rounded-pill">Price pending verification</span>@endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <h1 class="display-heading h1 text-burgundy">{{ $menuItem->name }}</h1>

                <p class="price fs-3 mb-2">
                    @if($menuItem->hasDiscount())
                        <span class="price-old">${{ number_format((float) $menuItem->price, 2) }}</span>
                    @endif
                    <span x-text="'$' + total"></span>
                </p>

                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach($menuItem->dietaryLabels as $label)
                        <span class="badge badge-diet rounded-pill">{{ $label->name }}</span>
                    @endforeach
                    @foreach($menuItem->allergens as $allergen)
                        <span class="badge badge-allergen rounded-pill">Contains {{ strtolower($allergen->name) }}</span>
                    @endforeach
                    @if($menuItem->prep_time_minutes)
                        <span class="badge text-bg-light rounded-pill border"><i class="bi bi-clock me-1" aria-hidden="true"></i>~{{ $menuItem->prep_time_minutes }} min</span>
                    @endif
                </div>

                @if($menuItem->description || $menuItem->short_description)
                    <p class="text-body-secondary">{{ $menuItem->description ?: $menuItem->short_description }}</p>
                @endif

                @unless($menuItem->isOrderable())
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                        This item is currently unavailable for online ordering.
                        @if($menuItem->available_from && $menuItem->available_until)
                            Available {{ \Carbon\Carbon::parse($menuItem->available_from)->format('g:i A') }}–{{ \Carbon\Carbon::parse($menuItem->available_until)->format('g:i A') }}.
                        @endif
                    </div>
                @endunless

                <form action="{{ route('cart.add', $menuItem) }}" method="post">
                    @csrf

                    @if($menuItem->variations->isNotEmpty())
                        <fieldset class="modifier-group mb-3">
                            <legend class="modifier-group-header h6 mb-0 w-100">
                                <span>Choose an option</span>
                                <span class="badge text-bg-danger rounded-pill">Required</span>
                            </legend>
                            <div class="p-3">
                                @foreach($menuItem->variations as $variation)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="variation_id" id="variation-{{ $variation->id }}"
                                               value="{{ $variation->id }}" x-model="variationId" @checked($loop->first)>
                                        <label class="form-check-label d-flex justify-content-between w-100" for="variation-{{ $variation->id }}">
                                            <span>{{ $variation->name }}</span>
                                            <span class="fw-bold">${{ number_format((float) $variation->price, 2) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    @foreach($menuItem->modifierGroups as $group)
                        <fieldset class="modifier-group mb-3">
                            <legend class="modifier-group-header h6 mb-0 w-100">
                                <span>{{ $group->name }}</span>
                                <span>
                                    @if($group->is_required)
                                        <span class="badge text-bg-danger rounded-pill">Required</span>
                                    @else
                                        <span class="badge text-bg-light border rounded-pill">Optional</span>
                                    @endif
                                    @if($group->selection_type === 'multiple' && $group->max_select)
                                        <span class="small text-muted">max {{ $group->max_select }}</span>
                                    @endif
                                </span>
                            </legend>
                            <div class="p-3">
                                @foreach($group->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input modifier-input"
                                               type="{{ $group->selection_type === 'single' ? 'radio' : 'checkbox' }}"
                                               name="modifiers[{{ $group->id }}{{ $group->selection_type === 'multiple' ? '_'.$option->id : '' }}]"
                                               id="option-{{ $option->id }}" value="{{ $option->id }}"
                                               data-price="{{ $option->price_adjustment }}"
                                               @checked($option->is_default && $group->selection_type === 'single')
                                               @if($group->is_required && $group->selection_type === 'single') required @endif
                                               @change="$nextTick(() => qty = qty)">
                                        <label class="form-check-label d-flex justify-content-between w-100" for="option-{{ $option->id }}">
                                            <span>{{ $option->name }}</span>
                                            @if((float) $option->price_adjustment != 0)
                                                <span class="text-muted">{{ (float) $option->price_adjustment > 0 ? '+' : '' }}${{ number_format((float) $option->price_adjustment, 2) }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach

                    <div class="mb-3">
                        <label for="item-notes" class="form-label fw-bold">Special instructions</label>
                        <textarea class="form-control" id="item-notes" name="notes" rows="2" maxlength="400"
                                  placeholder="e.g. scooped out, toasted dark, sauce on the side…"></textarea>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="qty-control" role="group" aria-label="Quantity">
                            <button type="button" @click="dec()" aria-label="Decrease quantity">−</button>
                            <input type="number" name="quantity" x-model.number="qty" min="1" max="25" aria-label="Quantity" inputmode="numeric">
                            <button type="button" @click="inc()" aria-label="Increase quantity">+</button>
                        </div>
                        <button type="submit" class="btn btn-brand btn-lg flex-grow-1 flex-sm-grow-0" @disabled(! $menuItem->isOrderable())>
                            <i class="bi bi-bag-plus-fill me-2" aria-hidden="true"></i>
                            Add to Cart — <span x-text="'$' + total"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($related->isNotEmpty())
            <section class="mt-5" aria-labelledby="related-heading">
                <h2 id="related-heading" class="section-heading h3 mb-3">You Might Also Like</h2>
                <div class="row g-3 g-lg-4">
                    @foreach($related as $item)
                        <div class="col-12 col-sm-6 col-lg-3 d-flex">
                            @include('partials.product-card', ['item' => $item])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
