@extends('layouts.public')

@section('seo_title')Your Cart — Sunset Bagel Exchange@endsection

@section('content')
    <div class="container py-4 py-lg-5">
        <h1 class="display-heading h1 text-burgundy mb-4">Your Cart</h1>

        @if(empty($items))
            <div class="text-center py-5">
                <i class="bi bi-bag display-1 text-gold" aria-hidden="true"></i>
                <p class="lead mt-3 mb-4">Your cart is empty — those bagels won't order themselves!</p>
                <a href="{{ route('menu.index') }}" class="btn btn-brand btn-lg">Browse the Menu</a>
            </div>
        @else
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="d-flex flex-column gap-3">
                        @foreach($items as $line)
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-3 col-sm-2">
                                            <img src="{{ \App\Services\ImageService::thumbUrl($line['image'] ?? null) }}"
                                                 class="rounded-3 w-100 object-cover" style="aspect-ratio: 1/1;"
                                                 alt="" loading="lazy">
                                        </div>
                                        <div class="col-9 col-sm-5">
                                            <h2 class="h6 fw-bold text-uppercase mb-1">
                                                <a class="text-decoration-none text-reset" href="{{ route('menu.item', $line['slug']) }}">{{ $line['name'] }}</a>
                                            </h2>
                                            @if($line['variation_name'])
                                                <div class="small text-body-secondary">{{ $line['variation_name'] }}</div>
                                            @endif
                                            @foreach($line['modifiers'] as $modifier)
                                                <div class="small text-body-secondary">
                                                    {{ $modifier['group_name'] }}: {{ $modifier['option_name'] }}
                                                    @if($modifier['price_adjustment'] > 0)
                                                        (+${{ number_format($modifier['price_adjustment'], 2) }})
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($line['notes'])
                                                <div class="small fst-italic text-body-secondary">"{{ $line['notes'] }}"</div>
                                            @endif
                                        </div>
                                        <div class="col-6 col-sm-3">
                                            <form action="{{ route('cart.update', $line['id']) }}" method="post" class="d-flex align-items-center gap-2">
                                                @csrf @method('PATCH')
                                                <div class="qty-control">
                                                    <button type="submit" name="quantity" value="{{ $line['quantity'] - 1 }}" aria-label="Decrease quantity of {{ $line['name'] }}">−</button>
                                                    <input type="text" value="{{ $line['quantity'] }}" readonly aria-label="Quantity of {{ $line['name'] }}">
                                                    <button type="submit" name="quantity" value="{{ $line['quantity'] + 1 }}" aria-label="Increase quantity of {{ $line['name'] }}">+</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-6 col-sm-2 text-end">
                                            <div class="price">${{ number_format($line['unit_price'] * $line['quantity'], 2) }}</div>
                                            <form action="{{ route('cart.remove', $line['id']) }}" method="post">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-link btn-sm text-danger p-0" type="submit">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Keep Shopping
                        </a>
                        <form action="{{ route('cart.clear') }}" method="post" data-confirm="Empty your entire cart?">
                            @csrf
                            <button class="btn btn-link text-danger" type="submit">Empty cart</button>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="summary-card p-4 position-sticky" style="top: 90px;">
                        <h2 class="h5 fw-bold text-uppercase mb-3">Order Summary</h2>

                        <form action="{{ route('cart.coupon.apply') }}" method="post" class="mb-3">
                            @csrf
                            <label class="form-label small fw-bold" for="coupon-code">Coupon code</label>
                            <div class="input-group">
                                <input id="coupon-code" type="text" name="code" class="form-control"
                                       value="{{ $coupon?->code }}" placeholder="e.g. WELCOME10" @disabled($coupon)>
                                @if($coupon)
                                    <button class="btn btn-outline-danger" form="remove-coupon-form" type="submit">Remove</button>
                                @else
                                    <button class="btn btn-outline-secondary" type="submit">Apply</button>
                                @endif
                            </div>
                        </form>
                        @if($coupon)
                            <form id="remove-coupon-form" action="{{ route('cart.coupon.remove') }}" method="post">@csrf @method('DELETE')</form>
                        @endif

                        <div class="summary-row"><span>Subtotal</span><span>${{ number_format($totals['subtotal'], 2) }}</span></div>
                        @if($totals['discount'] > 0)
                            <div class="summary-row text-success"><span>Discount ({{ $coupon?->code }})</span><span>−${{ number_format($totals['discount'], 2) }}</span></div>
                        @endif
                        <div class="summary-row"><span>Tax</span><span>${{ number_format($totals['tax'], 2) }}</span></div>
                        <hr>
                        <div class="summary-row summary-total"><span>Total</span><span>${{ number_format($totals['total'], 2) }}</span></div>
                        <p class="small text-body-secondary mb-3">Tip can be added at checkout.</p>

                        <a href="{{ route('checkout.show') }}" class="btn btn-brand btn-lg w-100">
                            Checkout <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
