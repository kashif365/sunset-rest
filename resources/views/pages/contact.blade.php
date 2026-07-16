@extends('layouts.public')

@section('seo_title')Contact & Location — Sunset Bagel Exchange | Ocean, NJ@endsection
@section('meta_description')Find Sunset Bagel Exchange at 3316 Sunset Ave., Ocean, NJ 07712. Call (732) 361-8119 or send us a message — hours, map and directions.@endsection

@section('content')
    <section class="section-burgundy py-5">
        <div class="container text-center">
            <p class="eyebrow text-yellow mb-1">Say hello</p>
            <h1 class="display-heading display-5 text-white">Contact &amp; Location</h1>
        </div>
    </section>
    @include('partials.curve', ['fill' => '#69001F', 'flip' => true])

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold text-uppercase mb-3">Send Us a Message</h2>
                        <form action="{{ route('contact.submit') }}" method="post" novalidate>
                            @csrf
                            <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="contact-name">Name *</label>
                                    <input class="form-control @error('name') is-invalid @enderror" id="contact-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="120">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="contact-email">Email *</label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="contact-email" type="email" name="email" value="{{ old('email') }}" required inputmode="email" autocomplete="email" maxlength="190">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="contact-phone">Phone</label>
                                    <input class="form-control @error('phone') is-invalid @enderror" id="contact-phone" type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" maxlength="30">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="contact-subject">Subject</label>
                                    <input class="form-control @error('subject') is-invalid @enderror" id="contact-subject" type="text" name="subject" value="{{ old('subject') }}" maxlength="190">
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-message">Message *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="contact-message" name="message" rows="5" required maxlength="3000">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-brand btn-lg" type="submit"><i class="bi bi-send-fill me-2" aria-hidden="true"></i>Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold text-uppercase mb-3">Find Us</h2>
                        <p class="mb-1"><i class="bi bi-geo-alt-fill text-gold me-2" aria-hidden="true"></i>{{ $siteSettings->get('business_address', '3316 Sunset Ave., Ocean, NJ 07712') }}</p>
                        <p class="mb-1"><i class="bi bi-telephone-fill text-gold me-2" aria-hidden="true"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->get('business_phone', '(732) 361-8119')) }}">{{ $siteSettings->get('business_phone', '(732) 361-8119') }}</a></p>
                        <p class="mb-3"><i class="bi bi-envelope-fill text-gold me-2" aria-hidden="true"></i><a href="mailto:{{ $siteSettings->get('business_email', 'sunsetbagelexchange@gmail.com') }}">{{ $siteSettings->get('business_email', 'sunsetbagelexchange@gmail.com') }}</a></p>

                        <h3 class="h6 fw-bold text-uppercase">Hours</h3>
                        <table class="hours-table w-100">
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
                </div>

                @if($siteSettings->get('map_embed_url'))
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden border">
                        <iframe src="{{ $siteSettings->get('map_embed_url') }}" title="Map to Sunset Bagel Exchange" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
