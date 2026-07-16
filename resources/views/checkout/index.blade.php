@extends('layouts.public')

@section('seo_title')Checkout — Sunset Bagel Exchange@endsection

@section('content')
    <div class="container py-4 py-lg-5"
         x-data='{
            slotsByDate: @json($slotsByDate),
            date: "{{ old('pickup_date', $dates[0] ?? '') }}",
            get slots() { return this.slotsByDate[this.date] ?? []; }
         }'>
        <h1 class="display-heading h1 text-burgundy mb-4">Checkout</h1>

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please fix the following:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="post" class="row g-4">
            @csrf
            <div class="col-12 col-lg-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold text-uppercase mb-3"><i class="bi bi-person-fill me-2 text-gold" aria-hidden="true"></i>Your Details</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="customer_name" class="form-label">Full name *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                                       name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name" maxlength="120">
                                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="customer_email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email"
                                       name="customer_email" value="{{ old('customer_email') }}" required autocomplete="email" inputmode="email" maxlength="190">
                                @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="customer_phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone"
                                       name="customer_phone" value="{{ old('customer_phone') }}" required autocomplete="tel" inputmode="tel" maxlength="30">
                                @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold text-uppercase mb-3"><i class="bi bi-clock-fill me-2 text-gold" aria-hidden="true"></i>Pickup Time</h2>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="pickup_date" class="form-label">Pickup date *</label>
                                <select class="form-select @error('pickup_date') is-invalid @enderror" id="pickup_date" name="pickup_date" x-model="date" required>
                                    @foreach($dates as $date)
                                        <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}{{ $date === today()->toDateString() ? ' (today)' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('pickup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="pickup_time" class="form-label">Pickup time *</label>
                                <select class="form-select @error('pickup_time') is-invalid @enderror" id="pickup_time" name="pickup_time" required>
                                    <template x-for="slot in slots" :key="slot">
                                        <option :value="slot" x-text="new Date('2000-01-01T' + slot).toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'})"
                                                :selected="slot === '{{ old('pickup_time') }}'"></option>
                                    </template>
                                </select>
                                @error('pickup_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold text-uppercase mb-3"><i class="bi bi-credit-card-fill me-2 text-gold" aria-hidden="true"></i>Payment</h2>
                        @foreach($gateways as $gateway)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay-{{ $gateway->identifier() }}"
                                       value="{{ $gateway->identifier() }}" @checked(old('payment_method', array_key_first($gateways)) === $gateway->identifier()) required>
                                <label class="form-check-label" for="pay-{{ $gateway->identifier() }}">{{ $gateway->label() }}</label>
                            </div>
                        @endforeach
                        <p class="small text-body-secondary mb-0">Online card payment is coming soon — for now you pay in store or by phone.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="h5 fw-bold text-uppercase mb-3"><i class="bi bi-chat-left-text-fill me-2 text-gold" aria-hidden="true"></i>Order Notes</h2>
                        <label for="notes" class="form-label visually-hidden">Order notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="1000"
                                  placeholder="Anything we should know? Allergies, cutting preferences, etc.">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="summary-card p-4 position-sticky" style="top: 90px;">
                    <h2 class="h5 fw-bold text-uppercase mb-3">Your Order</h2>
                    <ul class="list-unstyled mb-3">
                        @foreach($items as $line)
                            <li class="d-flex justify-content-between gap-2 py-1 border-bottom border-light-subtle">
                                <span>{{ $line['quantity'] }}× {{ $line['name'] }}@if($line['variation_name']) <span class="text-body-secondary small">({{ $line['variation_name'] }})</span>@endif</span>
                                <span class="text-nowrap">${{ number_format($line['unit_price'] * $line['quantity'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="summary-row"><span>Subtotal</span><span>${{ number_format($totals['subtotal'], 2) }}</span></div>
                    @if($totals['discount'] > 0)
                        <div class="summary-row text-success"><span>Discount ({{ $coupon?->code }})</span><span>−${{ number_format($totals['discount'], 2) }}</span></div>
                    @endif
                    <div class="summary-row"><span>Tax</span><span>${{ number_format($totals['tax'], 2) }}</span></div>

                    <div class="my-3">
                        <label for="tip" class="form-label fw-bold">Add a tip for the team 💛</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.50" min="0" max="500" class="form-control @error('tip') is-invalid @enderror"
                                   id="tip" name="tip" value="{{ old('tip', '0') }}" inputmode="decimal">
                            @error('tip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>
                    <div class="summary-row summary-total"><span>Total (before tip)</span><span>${{ number_format($totals['total'], 2) }}</span></div>
                    @if($minOrder > 0)
                        <p class="small text-body-secondary">Minimum order: ${{ number_format($minOrder, 2) }}</p>
                    @endif

                    <button type="submit" class="btn btn-brand btn-lg w-100 mt-2">
                        <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>Place Pickup Order
                    </button>
                    <p class="small text-body-secondary text-center mt-2 mb-0">You'll get an email confirmation with your order number.</p>
                </div>
            </div>
        </form>
    </div>
@endsection
